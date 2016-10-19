<?php

//namespace Habari;

class HConsole extends Plugin
{
	private $code = array();
	private $sql = false;
	private $htmlspecial = false;

	public function alias()
	{
		return array(
			'template_footer' => array('action_admin_footer', 'action_template_footer')
		);
	}

	/**
	 * Early as possible, let's define DEBUG so we get DEBUG output and turn on error display;
	 * But only if we have code to execute.
	 */
	public function action_plugins_loaded()
	{
		if (!empty($_POST['hconsole_code'])) {
			if (!defined('Habari\DEBUG')) {
				define('Habari\DEBUG', true);
			}
			ini_set('display_errors', 'on');
		}
	}

	public function action_init()
	{
		$user = User::identify();
		if ($user->loggedin && $user->can('super_user')) {
			Stack::add('template_header_javascript', Site::get_url('scripts') . '/jquery.js', 'jquery');
			Stack::add('template_stylesheet', array($this->get_url(true) . 'hconsole.css', 'screen'));
			Stack::add('admin_stylesheet', array($this->get_url(true) . 'hconsole.css', 'screen'));
			if ($_POST->raw('hconsole_code')) {
				$wsse = Utils::WSSE($_POST['nonce'], $_POST['timestamp']);
				if ($_POST['PasswordDigest'] == $wsse['digest']) {
					if (isset($_POST['sql']) && $_POST['sql'] == 'RUN SQL') {
						$this->sql = rawurldecode($_POST->raw('hconsole_code'));
						return;
					}
					if (isset($_POST['htmlspecial']) && $_POST['htmlspecial'] == 'true') {
						$this->htmlspecial = true;
					}
					$this->code = $this->parse_code(rawurldecode($_POST->raw('hconsole_code')));
					foreach ($this->code['hooks'] as $i => $hook) {
						$functions = $this->get_functions($hook['code']);
						if (empty($functions)) {
							trigger_error("Parse Error in $i. No function to register.", E_USER_WARNING);
						} else {
							eval($hook['code']);
							foreach ($functions as $function) {
								if ($i == 'action_init') {
									call_user_func($function);
								} else {
									Plugins::register($function, $hook['type'], $hook['hook']);
								}
							}
						}
					}
				}
			}
		}
	}

	public function action_hconsole_debug()
	{
		if (isset($this->code['debug'])) {
			ob_start();
			$res = eval($this->code['debug']);
			$dat = ob_get_contents();
			ob_end_clean();
			if ($res === false) {
				throw Error::raise($dat, E_COMPILE_ERROR);
			} else {
				echo $this->htmlspecial ? htmlspecialchars($dat) : $dat;
			}
		}
		if ($this->sql) {
			$itemlist = array();
			if (preg_match('#^\s*(select|show).*#i', $this->sql)) {
				$data = DB::get_results($this->sql);
				if (DB::has_errors()) throw Error::raise(DB::get_last_error());
				if (is_array($data) && count($data)) {
					self::sql_dump($data);
				} else {
					echo 'empty set, nothing returned.';
				}
			} else {
				$data = DB::query($this->sql);
				if (DB::has_errors()) throw Error::raise(DB::get_last_error());
				echo 'Result: ' . (string)$data;
			}

		}
	}

	public static function sql_dump($array)
	{
		$keys = array_keys($array[0]->to_array());

		echo "<table><tr>";
		foreach ($keys as $key) {
			echo "<th><b>$key</b></th>";
		}
		echo '</tr>';
		foreach ($array as $i => $query_record) {
			$alt = $i % 2 ? "class='alt'" : '';
			echo "<tr $alt>";
			foreach ($query_record->to_array() as $a) {
				echo '<td>' . htmlspecialchars(substr((string)$a, 0, 500)) . '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}

	private function get_functions($code)
	{
		$tokens = token_get_all("<?php $code ?>");
		$functions = array();
		foreach ($tokens as $i => $token) {
			if (is_array($token) && $token[0] == T_FUNCTION) {
				if ($tokens[$i + 1][0] == T_STRING) {
					$functions[] = $tokens[$i + 1][1];
				} elseif ($tokens[$i + 1][0] == T_WHITESPACE && $tokens[$i + 2][0] == T_STRING) {
					$functions[] = $tokens[$i + 2][1];
				}
			}
		}
		return $functions;
	}

	private function parse_code($code)
	{
		$tokens = token_get_all("<?php $code ?>");
		$hooks = array();
		$debug = array();
		$flag = false;
		$braket = 1;

		for ($i = 0; $i < count($tokens); $i++) {
			$token = $tokens[$i];
			if ($flag) {
				if ($braket == 0) {
					$hooks[$flag]['end'] = $i - 1;
					$flag = false;
					$braket = 1;
				}
				if ($token == '}') {
					$braket--;
				} elseif ($token == '{') {
					$braket++;
				}
				continue;
			}
			if (is_array($token) && $token[0] == T_STRING && preg_match('@^(action|filter|theme|xmlrpc)_(.+)$@i', $token[1], $m)) {
				$hooks[$m[0]]['hook'] = $m[2];
				$hooks[$m[0]]['type'] = $m[1];
				$flag = $m[0];
				if ($tokens[$i + 1] == '{') {
					$hooks[$m[0]]['start'] = $i + 2;
					$i += 3;
				} elseif ($tokens[$i + 1][0] == T_WHITESPACE && $tokens[$i + 2] == '{') {
					$hooks[$m[0]]['start'] = $i + 3;
					$i += 2;
				} else {
					trigger_error("Parse Error in $flag", E_USER_ERROR);
				}
			} elseif (is_array($token) && ($token[0] == T_CLOSE_TAG || $token[0] == T_OPEN_TAG)) {
				continue;
			} else {
				$debug[] = $token;
			}
		}

		foreach ($hooks as $i => $hook) {
			if (empty($hook['end'])) {
				trigger_error("Parse Error in $i. No closing braket", E_USER_ERROR);
				unset($hooks[$i]);
				continue;
			}
			$toks = array_slice($tokens, $hook['start'], $hook['end'] - $hook['start']);
			$hooks[$i]['code'] = '';
			foreach ($toks as $token) {
				$hooks[$i]['code'] .= is_array($token) ? $token[1] : $token;
			}
		}
		return array(
			'hooks' => $hooks,
			'debug' => implode(array_map(create_function('$a', 'return is_array($a)?$a[1] : $a;'), $debug))
		);
	}

	public function action_admin_theme_get_hconsole()
	{

	}

	public function filter_admin_access_tokens(array $require_any, $page)
	{
		switch ($page) {
			case 'hconsole':
				$require_any = array('super_user' => true);
				break;
		}
		return $require_any;
	}

	/**
	 * @TODO clean up this html and code here.
	 */
	public function template_footer()
	{
		$user = User::identify();
		if ($user->loggedin && $user->can('super_user')) {
			$wsse = Utils::wsse();
			$code = $_POST->raw('hconsole_code');
			$display = empty($_POST['hconsole_code']) ? 'display:none;' : '';
			$htmlspecial = isset($_POST['htmlspecial']) ? 'checked="true"' : '';
			$sql = isset($_POST['sql']) ? 'checked="true"' : '';

			echo <<<GOO
<div id="hconsole_button">
	<a href="#" onclick="jQuery('#hconsole').toggle('slow'); return false;">^ HConsole</a>
</div>
<div  id="hconsole" style="$display">
GOO;
			if ($this->code || $this->sql) {
				echo '<pre class="resizable" id="hconsole_debug">';

				try {
					Plugins::act('hconsole_debug');
				} catch (\Exception $e) {
					Error::exception_handler($e);
				}
				echo '</pre>';
			}
			echo <<<MOO
<form method="post" action="" id="hconsole_form">
	<textarea cols="100" rows="7" name="hconsole_code">{$code}</textarea><br>
	<div id="hconsole_edit_filler">
		<div id="hconsole_edit"></div>
	</div>
	<input type='submit' value='RUN' style="clear:both" />
	<input type='checkbox' name='htmlspecial' value='true' $htmlspecial />htmlspecialchars
	<input type='checkbox' name='sql' value="RUN SQL" $sql />SQL
	<input type="hidden" id="nonce" name="nonce" value="{$wsse['nonce']}">
	<input type="hidden" id="timestamp" name="timestamp" value="{$wsse['timestamp']}">
	<input type="hidden" id="PasswordDigest" name="PasswordDigest" value="{$wsse['digest']}">
</form>
<script src="http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
var editor = ace.edit("hconsole_edit");
var textarea = $('textarea[name="hconsole_code"]').hide();
$('input[name="sql"]').on('click', sqlCheck);
function sqlCheck (){
  if ($('input[name="sql"]').attr('checked')) {
	editor.getSession().setMode('ace/mode/sql');
  }
  else {
	editor.getSession().setMode('ace/mode/php');
  }
}
$(document).ready(function(){sqlCheck();});
editor.getSession().setValue(textarea.val());
editor.getSession().on('change', function(){
  textarea.val(editor.getSession().getValue());
});
editor.setTheme("ace/theme/twilight");
editor.getSession().setMode("ace/mode/php");
editor.commands.addCommand({
	name: 'Run Code',
	bindKey: {win: 'Ctrl-Q',  mac: 'Command-Q'},
	exec: function(editor) {
		$('#hconsole_form').submit();
	},
	readOnly: true // false if this command should not apply in readOnly mode
});
</script></div>
MOO;
		}
	}
}

?>

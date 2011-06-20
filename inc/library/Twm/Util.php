<?php

class Twm_Util {

	static function replace_plain_text_link($plain_text) {
		$url_html = preg_replace(
			'/(?<!S)((http(s?):\/\/)|(www.))+([\w.1-9\&=#?\-~%;\/]+)/', '<a target="_blank" href="http$3://$4$5">http$3://$4$5</a>', $plain_text);
		return ($url_html);
	}
}
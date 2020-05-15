<?php

namespace ctocode\sdks\mail;

/**
 * 自己弄个 邮件发送
 * @author ctocode-zhw
 *
 */
class MailSdkCtocode
{
	public static function sendMail()
	{
		$mail = new \ctocode\library\SendMail ();
		$mail->setServer ( "smtp.qq.com", "xxx@qq.com", "pass" );
		$mail->setFrom ( "xxx@qq.com" );
		$mail->setReceiver ( "xxx@qq.com" );
		$mail->setMailInfo ( "test", "<b>test</b>" );
		$mail->sendMail ();
	}
}

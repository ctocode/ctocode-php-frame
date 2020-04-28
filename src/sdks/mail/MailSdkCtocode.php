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
		$mail->setServer ( "smtp.qq.com", "1838188896@qq.com", "pass" );
		$mail->setFrom ( "1838188896@qq.com" );
		$mail->setReceiver ( "632522043@qq.com" );
		$mail->setReceiver ( "632522043@qq.com" );
		$mail->setMailInfo ( "test", "<b>test</b>" );
		$mail->sendMail ();
	}
}

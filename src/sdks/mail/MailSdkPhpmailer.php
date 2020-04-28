<?php

namespace ctocode\sdks\mail;

/**
 * 阿里云 邮件发送
 * @author ctocode-zhw
 *
 */
class MailSdkPhpmailer
{
	/**
	 * 发送邮件
	 * @author  xjj
	 * @version 2017-12-25
	 *
	 * @param string $to_email        邮件的接收地址
	 * @param string $name            发件人名称
	 * @param string $to_email_body   邮件的内容
	 * @param string $to_email_title  邮件的标题
	 * @return boolean|string
	 */
	public static function sendMail($settConf = [], $sendArr = [])
	{
		/*
		 * $base= dirname(__FILE__).'/PHPMailer/';
		 * require_once($base . 'class.phpmailer.php');
		 * require_once($base . "class.smtp.php");
		 * $mail = new \PHPMailer();
		 */
		require_once _CTOCODE_EXTEND_ . '/PHPMailer/Phpmailer.class.php';
		$mail = new \Lib\PHPMailer\Phpmailer ();
		$mail->CharSet = "UTF-8"; // 设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置为 UTF-8
		$mail->IsSMTP (); // 设定使用SMTP服务
		$mail->SMTPAuth = true; // 启用 SMTP 验证功能
		$mail->SMTPSecure = "ssl"; // SMTP 安全协议

		$mail->Host = $settConf['email_smtp_host']; // SMTP 服务器
		$mail->Port = ! empty ( $settConf['email_smtp_host'] ) ? $settConf['email_smtp_host'] : 465; // SMTP服务器的端口号
		$mail->Username = $settConf['email_smtp_account']; // SMTP服务器用户名
		$mail->Password = $settConf['email_smtp_pass']; // SMTP服务器密码
		$mail->SetFrom ( $settConf['email_smtp_account'], $sendArr['email_name'] ); // 设置发件人地址和名称
		$mail->AddReplyTo ( $settConf['email_smtp_account'], $sendArr['email_name'] );
		// 设置邮件回复人地址和名称
		$mail->Subject = $sendArr['email_sendtitle']; // 设置邮件标题
		$mail->AltBody = "为了查看该邮件，请切换到支持 HTML 的邮件客户端"; // 可选项，向下兼容考虑
		$mail->MsgHTML ( $sendArr['email_sendbody'] ); // 设置邮件内容
		$mail->AddAddress ( $sendArr['email_sendemail'], '' );
		// $mail->AddAttachment("images/phpmailer.gif"); // 附件
		return $mail->Send () ? true : $mail->ErrorInfo;
	}
}

<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 1/16/2015
 * Time: 10:22 AM
 */

namespace Email;


class mpMailer
{
	private $logger;

	public function __construct(PsrLogLoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function sendEmail($emailAddress)
	{
		// code to send an email...

		// log a message
		$this->logger->info("Email sent to $emailAddress");
	}

	public function prepare_email( $type, $email_addr ){
		switch ( $type ){
			case 'renewal':
				$this->get_renewal_template();
				$this->sendEmail( $email_addr );
				break;
			case 'new_member':
				$this->get_new_member_template();
				$this->sendEmail( $email_addr );
				break;
			case 'ppball':
				$this->get_pp_ball_template();
				$this->sendEmail( $email_addr );
		}
	}

	private function get_renewal_template(){
		$this->body = "<div>this is just for testing right now.</div>";

		// log a message
		$this->logger->info("<div>Email to be sent contains:</div><div>$this->boday</div>");
	}

	private function get_new_member_template(){
		$this->body = "<div>This is a test of the new member email.  I will be triggered by paypal payment verification</div>";
		$this->logger->info("<div>Email to be sent contains:</div><div>$this->boday</div>");
	}

	private function get_pp_ball_template(){
		$this->body = "<div>This is a test of the Pirate's Ball Registration System.  This will be triggered only when paypal payments have been verified<br>";
		$this->body .= "This wiil include emails to the purchaser/registrant and the Pirate's Ball Comity head as well as the treasurer. </div>";
		$this->logger->info("<div>Email to be sent contains:</div><div>$this->boday</div>");
	}
}
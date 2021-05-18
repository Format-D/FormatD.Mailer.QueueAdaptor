<?php
namespace FormatD\Mailer\QueueAdaptor\Aspect;

/*                                                                        *
 * This script belongs to the Flow package "FormatD.Mailer.QueueAdaptor". *
 *                                                                        */

use FormatD\Mailer\QueueAdaptor\Job\Context;
use FormatD\Mailer\QueueAdaptor\Job\MailJob;
use Flowpack\JobQueue\Common\Job\JobManager;
use Neos\Flow\Annotations as Flow;
use Neos\SwiftMailer\Message;


/**
 * @Flow\Aspect
 * @Flow\Introduce("class(Neos\SwiftMailer\Message)", traitName="FormatD\Mailer\QueueAdaptor\Traits\QueueNameTrait")
 */
class QueuingAspect {

	/**
	 * @var JobManager
	 * @Flow\Inject
	 */
	protected $jobManager;

	/**
	 * @Flow\Inject
	 * @var Context
	 */
	protected $jobContext;

	/**
	 * @Flow\InjectConfiguration
	 * @var array
	 */
	protected $settings;

	/**
	 * Intercept all emails or add bcc according to package configuration
	 *
	 * @param \Neos\Flow\Aop\JoinPointInterface $joinPoint
	 * @Flow\Around("setting(FormatD.Mailer.QueueAdaptor.enableAsynchronousMails) && method(Neos\SwiftMailer\Message->send())")
	 * @return void
	 */
	public function queueEmails(\Neos\Flow\Aop\JoinPointInterface $joinPoint) {

		if ($this->jobContext->isMailQueueingDisabled()) {
			return $joinPoint->getAdviceChain()->proceed($joinPoint);
		}

		/** @var Message $email */
		$email = $joinPoint->getProxy();
		$job = new MailJob($email);
		$this->jobManager->queue($email->getQueueName() ? $email->getQueueName() : 'fdmailer-mail-queue', $job);

		// Neos\SwiftMailer\Message->send() should return the number of recipients who were accepted for delivery
		// We dont know that until mail is execured by queue so we assume every recipient was accepted
		// @todo: read recipient count and return that
		return 1;
	}

}

?>

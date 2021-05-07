<?php

namespace FormatD\Mailer\QueueAdaptor\Job;

use Neos\Flow\Annotations as Flow;
use Flowpack\JobQueue\Common\Job\JobInterface;
use Flowpack\JobQueue\Common\Queue\QueueInterface;
use Flowpack\JobQueue\Common\Queue\Message;

class MailJob implements JobInterface {

	/**
	 * @Flow\Inject
	 * @var Context
	 */
	protected $jobContext;

	/**
	 * @var \Neos\SwiftMailer\Message
	 */
	protected $email = null;

	/**
	 * MailJob constructor.
	 * @param \Neos\SwiftMailer\Message $email
	 */
	public function __construct(\Neos\SwiftMailer\Message $email) {
		$this->email = $email;
	}

	/**
	 * Execute the job
	 *
	 * A job should finish itself after successful execution using the queue methods.
	 *
	 * @param QueueInterface $queue
	 * @param Message $message The original message
	 * @return bool TRUE if the job was executed successfully and the message should be finished
	 */
	public function execute(QueueInterface $queue, Message $message): bool {

		$message = $this->email;

		$this->jobContext->withoutMailQueuing(function () use ($message) {
			$message->send();
		});

		return TRUE;
	}

	/**
	 * Get a readable label for the job
	 *
	 * @return string A label for the job
	 */
	public function getLabel(): string {
		return $this->email->getSubject();
	}

}

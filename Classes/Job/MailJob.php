<?php

namespace FormatD\Mailer\QueueAdaptor\Job;

use Neos\Cache\Frontend\StringFrontend;
use Neos\Flow\Annotations as Flow;
use Flowpack\JobQueue\Common\Job\JobInterface;
use Flowpack\JobQueue\Common\Queue\QueueInterface;
use Flowpack\JobQueue\Common\Queue\Message;

class MailJob implements JobInterface {

	/**
	 * @Flow\InjectConfiguration(type="Settings", package="FormatD.Mailer.QueueAdaptor", path="serializationCache")
	 * @var array
	 */
	protected $serializationCacheSettings;

	/**
	 * @Flow\Inject
	 * @var Context
	 */
	protected $jobContext;

	/**
	 * @Flow\Inject
	 * @var StringFrontend
	 */
	protected $mailSerializationCache;

	/**
	 * @var \Neos\SwiftMailer\Message
	 */
	protected $email = null;

	/**
	 * @var string
	 */
	protected $emailSerializationCacheIdentifier = null;

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

		$message = $this->getEmail();

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
		return $this->getEmail()->getSubject();
	}

	/**
	 * Serialize the email to a file because it can get really big with attachments
	 *
	 * @return string[]
	 * @throws \Neos\Cache\Exception
	 * @throws \Neos\Cache\Exception\InvalidDataException
	 */
	public function __sleep()
	{
		if ($this->serializationCacheSettings['enabled']) {
			$this->emailSerializationCacheIdentifier = uniqid('email-');
			$this->mailSerializationCache->set($this->emailSerializationCacheIdentifier, serialize($this->email), [], 172800); // 48 Std. lifetime
			return array('emailSerializationCacheIdentifier');
		}

		return array('email');
	}

	/**
	 * Restores the serialized email if cached in file
	 *
	 * @return \Neos\SwiftMailer\Message
	 */
	protected function getEmail() {
		if (!$this->email && $this->emailSerializationCacheIdentifier && $this->mailSerializationCache->has($this->emailSerializationCacheIdentifier)) {
			$this->email = unserialize($this->mailSerializationCache->get($this->emailSerializationCacheIdentifier));
		}
		return $this->email;
	}

}

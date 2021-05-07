<?php

namespace FormatD\Mailer\QueueAdaptor\Job;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class Context {

	/**
	 * @var bool
	 */
	protected $mailQueueingDisabled = false;

	/**
	 * @return bool
	 */
	public function isMailQueueingDisabled() {
		return $this->mailQueueingDisabled;
	}

	/**
	 * Lets you switch off mail queueing for the runtime of $callback
	 *
	 * Usage:
	 * $this->jobContext->withoutMailQueuing(function () use ($message) {
	 *   $message->send();
	 * });
	 *
	 * @param \Closure $callback
	 * @return void
	 * @throws \Exception
	 */
	public function withoutMailQueuing(\Closure $callback)
	{
		$mailQueueingIsAlreadyDisabled= $this->mailQueueingDisabled;
		$this->mailQueueingDisabled = true;
		try {
			/** @noinspection PhpUndefinedMethodInspection */
			$callback->__invoke();
		} catch (\Exception $exception) {
			$this->mailQueueingDisabled = false;
			throw $exception;
		}
		if ($mailQueueingIsAlreadyDisabled === false) {
			$this->mailQueueingDisabled = false;
		}
	}

}

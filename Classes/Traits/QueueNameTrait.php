<?php
namespace FormatD\Mailer\QueueAdaptor\Traits;

/*                                                                        *
 * This script belongs to the Flow package "FormatD.Mailer.QueueAdaptor". *
 *                                                                        */

use Neos\Flow\Annotations as Flow;


trait QueueNameTrait {

	/**
	 * @var string
	 */
	protected $queueName = null;

	/**
	 * @return string
	 */
	public function getQueueName(): ?string
	{
		return $this->queueName;
	}

	/**
	 * @param string $queueName
	 */
	public function setQueueName(?string $queueName): void
	{
		$this->queueName = $queueName;
	}

}

?>

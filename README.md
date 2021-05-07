
FormatD.Mailer.QueueAdaptor
==========

This package changes the mail delivery in Neos (`neos/swiftmailer`) to asynchronously send mails via a queue.
The idea is to make it work as a plug-and-play replacement for every mail generated in the system.

Disclaimer
----------

This Package is just a proof of concept and needs a patch for `neos/swiftmailer` to work
(contained in this packageand applied automatically by `cweagans/composer-patches`).
The patch is neccessary because the Message object of `neos/swiftmailer` cannot be serialized. 
The patch changes the implementation from inheritance to a decorator pattern.

Setup
----------

### Choose a queue backend

This Packages uses flowpack/jobqueue-common (https://github.com/Flowpack/jobqueue-common) to set up a mail queue.
You can choose a backend of your taste, install it via composer and then override the `className` in the configuration:

	Flowpack:
	  JobQueue:
	    Common:
	      queues:
            'fdmailer-mail-queue':
              className: 'Flowpack\JobQueue\Doctrine\Queue\DoctrineQueue'


To use the default db backend just install `flowpack/jobqueue-doctrine`.

### Setup queue

The queue must be set up with this command. See documentation here for details: https://github.com/Flowpack/jobqueue-common.

	./flow queue:setup fdmailer-mail-queue


### Start worker for queue

After these steps mails are put into the queue instead of beeing sent directly during the request.
To send the queued mails run a worker cronjob on CLI:

	# work on max 25 jobs in max 50 sec
	./flow job:work fdmailer-mail-queue --limit 25 --exitAfter 50


Now test if it is working:

	./flow email:send --body "Hello World" from@example.com to@example.com "My Test Mail"
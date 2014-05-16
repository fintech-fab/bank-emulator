<?php


namespace FintechFab\BankEmulator\Components\Processor;


use FintechFab\BankEmulator\Components\Helpers\Curl;
use Illuminate\Mail\Message;
use Illuminate\Queue\Jobs\Job;
use Log;
use Mail;

class CallbackQueue
{


	public function fire(Job $job, $data)
	{

		$doSuccess = false;

		// push to url
		if ($data['url']) {
			$curl = new Curl();
			$curl->post($data['url'], $data['data']);
			$doSuccess = $curl->ok();

			if (!$doSuccess) {
				Log::warning('callback.push.url', array(
					'message' => 'callback push failed',
					'order'   => $data['data']['order'],
				));
			}
		}

		// push to email
		if ($data['email']) {

			Mail::send(array('text' => 'ff-bank-em::demo.email_callback'), $data, function (Message $message) use ($data) {
				$message->to($data['email'])->subject('Bank Emulator Payment Message');
			});
			$doSuccess = (0 == count(Mail::failures()));

			if (!$doSuccess) {
				Log::warning('callback.push.email', array(
					'message' => 'callback push failed',
					'order'   => $data['data']['order'],
				));
			}
		}

		// release, if error
		if ($doSuccess) {

			Log::info('callback.push', array(
				'order' => $data['data']['order'],
			));
			$job->delete();

		} else {

			Log::warning('callback.push', array(
				'order' => $data['data']['order'],
			));
			if ($job->attempts() > 10) {
				$job->delete();
			} else {
				$job->release(60);
			}

		}

	}

} 
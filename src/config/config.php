<?php

return array(
	'user_id' => Auth::user() ? Auth::user()->getAuthIdentifier() : null,
);
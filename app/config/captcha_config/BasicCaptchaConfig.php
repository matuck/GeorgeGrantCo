<?php if (!class_exists('CaptchaConfiguration')) { return; }

// BotDetect PHP Captcha configuration options
$LBD_CaptchaConfig = \CaptchaConfiguration::GetSettings();
$LBD_CaptchaConfig->CodeLength = \CaptchaRandomization::GetRandomCodeLength(3, 4);
$LBD_CaptchaConfig->CodeStyle = CodeStyle::Numeric;
<?php
/*
 * Resolves the "Enable saving cards" setting into one of three modes, and
 * applies the one rule that overrides the merchant: a guest has no customer
 * account for a token to belong to, so nothing is ever saved for one.
 *
 * The setting used to be a boolean named forced_tokenization. This extension has
 * no upgrade hook - install() only creates tables, and runs once - so the legacy
 * value is translated here, on read, instead of by a migration. An empty
 * card_saving means the merchant has not seen the new dropdown yet and the old
 * boolean still carries their intent: on became forced, off became disabled.
 */
class WorldlineCardSaving {
	const FORCED = 'forced';
	const ENABLED = 'enabled';
	const DISABLED = 'disabled';

	/*
	 * What the merchant configured, ignoring who is checking out.
	 */
	public static function getMode($setting) {
		$mode = '';

		if (!empty($setting['advanced']['card_saving'])) {
			$mode = $setting['advanced']['card_saving'];
		}

		if (in_array($mode, array(self::FORCED, self::ENABLED, self::DISABLED))) {
			return $mode;
		}

		if (!empty($setting['advanced']['forced_tokenization'])) {
			return self::FORCED;
		}

		return self::DISABLED;
	}

	/*
	 * What actually applies to this checkout. Guest checkout collapses every
	 * mode to disabled, which is what keeps the consent prompt hidden, the
	 * saved-card list empty and tokenization off in both payment flows.
	 */
	public static function getEffectiveMode($setting, $logged) {
		if (!$logged) {
			return self::DISABLED;
		}

		return self::getMode($setting);
	}
}

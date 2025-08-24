<?php

namespace VdubDev\CsvPreviewConvert\TwigCs;

use FriendsOfTwig\Twigcs\Rule\ForbiddenFunctions;
use FriendsOfTwig\Twigcs\Rule\LowerCaseVariable;
use FriendsOfTwig\Twigcs\Rule\RuleInterface;
use FriendsOfTwig\Twigcs\Rule\UnusedVariable;
use FriendsOfTwig\Twigcs\Ruleset\Official;
use FriendsOfTwig\Twigcs\Validator\Violation;

class CustomRuleset extends Official
{
    /**
     * @return array<RuleInterface>
     */
    public function getRules(): array
    {
        $officialRules = parent::getRules();

        foreach ($officialRules as $key => $officialRule) {
            // Remove the LowerCaseVariable rule as it introduces incoherency since controller variables are camelCased.
            if ($officialRule instanceof LowerCaseVariable) {
                unset($officialRules[$key]);

                continue;
            }

            if ($officialRule instanceof UnusedVariable) {
                unset($officialRules[$key]);
            }
        }

        return array_merge($officialRules, [new ForbiddenFunctions(Violation::SEVERITY_ERROR, ['dump'])]);
    }
}

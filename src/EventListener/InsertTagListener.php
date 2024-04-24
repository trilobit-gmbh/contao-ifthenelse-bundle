<?php

declare(strict_types=1);

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 */

namespace Trilobit\IfthenelseBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Date;
use Contao\System;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class InsertTagListener
{
    /**
     * Class ReplaceInsertTags.
     *
     * @Hook("replaceInsertTags")
     */
    public function __invoke(string $tag)
    {
        $chunks = explode('::', $tag);

        if ('sel' !== $chunks[0]) {
            return false;
        }

        return $this->isVisible($chunks[1]) ? $chunks[2] ?? '' : $chunks[3] ?? '';
    }

    public function isVisible($expression): bool
    {
        if (empty($expression)) {
            return true;
        }

        $tokenChecker = System::getContainer()->get('contao.security.token_checker');

        $config['member'] = new \stdClass();
        if (null !== $tokenChecker
            && $tokenChecker->hasFrontendUser()
        ) {
            $config['member'] = FrontendUser::getInstance();
        }

        $config['app'] = new \stdClass();
        $config['app']->tools = new App();

        $config['app']->isoDate = Date::parse('Y-m-d');
        $config['app']->isoTime = Date::parse('H:i');

        $config['app']->minute = Date::parse('i');
        $config['app']->hour = Date::parse('H');
        $config['app']->day = Date::parse('d');
        $config['app']->month = Date::parse('m');
        $config['app']->year = Date::parse('Y');

        $config['app']->date = Date::parse('Ymd');
        $config['app']->time = Date::parse('Hi');

        $config['app']->tstamp = time();

        $expression = trim(str_replace(
            [
                '&#39;',
                '&#60;',
                '&#61;',
                '&#62;',

                '&lt;',
                '&gt;',

                'OR',
                'AND',
            ],
            [
                '\'',
                '<',
                '=',
                '>',

                '<',
                '>',

                '||',
                '&&',
            ],
            $expression
        ));

        try {
            return (bool) (new ExpressionLanguage())->evaluate(
                !empty($expression)
                    ? html_entity_decode($expression)
                    : '',
                $config
            );
        } catch (\Exception $exception) {
            return false;
        }
    }
}

class App
{
    public function dateDiff($dateA, $dateB, $format = 'days')
    {
        if (empty($dateA) || empty($dateB)) {
            return 0;
        }

        if (is_numeric($dateA)) {
            $dateA = Date::parse('Y-m-d', $dateA);
        }

        if (is_numeric($dateB)) {
            $dateB = Date::parse('Y-m-d', $dateB);
        }

        $datimA = date_create($dateA);
        $datimB = date_create($dateB);

        $diff = date_diff($datimA, $datimB);

        return $diff->{$format};
    }
}

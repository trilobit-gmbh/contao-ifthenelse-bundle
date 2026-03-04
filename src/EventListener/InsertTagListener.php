<?php

declare(strict_types=1);

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 */

namespace Trilobit\IfthenelseBundle\EventListener;

use Contao\Date;
use Contao\FrontendUser;
use Contao\PageModel;
use Contao\System;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class InsertTagListener
{
    /**
     * Class ReplaceInsertTags.
     */
    #[\Contao\CoreBundle\DependencyInjection\Attribute\AsHook('replaceInsertTags')]
    public function __invoke(string $tag)
    {
        $chunks = explode('::', $tag);

        if ('sel' !== $chunks[0]) {
            return false;
        }

        return $this->isVisible($chunks[1])
            ? $chunks[2] ?? ''
            : $chunks[3] ?? '';
    }

    public function isVisible($expression): bool
    {
        if (empty($expression)) {
            return true;
        }

        $container = System::getContainer();

        $tokenChecker = $container->get('contao.security.token_checker');

        $config['member'] = new \stdClass();
        if (null !== $tokenChecker
            && $tokenChecker->hasFrontendUser()
        ) {
            $config['member'] = FrontendUser::getInstance();
        }

        $request = $container->get('request_stack');

        $config['page'] = $request->getCurrentRequest()->get('pageModel');

        $config['app'] = new \stdClass();
        $config['app']->tools = new App();

        $config['app']->isoDate = Date::parse('Y-m-d');
        $config['app']->isoTime = Date::parse('H:i');

        $config['app']->minute = (int) Date::parse('i');
        $config['app']->hour = (int) Date::parse('H');
        $config['app']->day = (int) Date::parse('d');
        $config['app']->month = (int) Date::parse('m');
        $config['app']->year = (int) Date::parse('Y');

        $config['app']->date = (int) Date::parse('Ymd');
        $config['app']->time = (int) Date::parse('Hi');

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

<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2014 Leo Feyer
 *
 * @package     Trilobit
 * @author      trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license     LGPL-3.0-or-later
 * @copyright   trilobit GmbH
 */

namespace Trilobit\IfthenelseBundle;

use Contao\Controller;
use Contao\Input;
use Contao\Template;

/**
 * Class HookOutputFrontendTemplate
 * @package Trilobit\IfthenelseBundle
 */
class HookOutputFrontendTemplate extends Template
{

    /**
     * @param $varValue
     * @param bool $blnClean
     * @return mixed|string
     */
    protected function searchReplace($varValue, $blnClean=true)
    {
        $varValue = str_replace(
            array(
                '__BRCL__', '__BRCR__',
                '[gt]', '&gt;', '&#62;',
                '[lt]', '&lt;', '&#60;',
                '[&]', '&amp;', '&#38;',
                '&#61;',
            ),
            array(
                '{{', '}}',
                '>', '>', '>',
                '<', '<', '<',
                '&', '&', '&',
                '=',
            ),
            $varValue
        );

        $varValue = trim($varValue);

        $varValue = Controller::replaceInsertTags($varValue, false);

        if ($blnClean)
        {
            $varValue = Input::cleanKey($varValue);
            $varValue = Input::encodeSpecialChars($varValue);
        }

        $varValue = str_replace("'", "\'", $varValue);

        return $varValue;
    }


    /**
     * @param $strBuffer
     * @param $objTemplate
     * @return mixed|string
     * @throws \Exception
     */
    public function replaceConditionalTags($strBuffer, $objTemplate)
    {
        $strBuffer = str_replace(array('{{', '}}'), array('__BRCL__', '__BRCR__'), $strBuffer);

        $arrTags = preg_split('/(\{[^}]+\})/sim', $strBuffer, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

        if (!empty($arrTags))
        {
            $blnEval = false;
            $strReturn = '';

            $arrOperations = array(
                '<',
                '<=',
                '>',
                '>=',
                '==',
                '===',
                '!=',
                '!==',
            );

            // Replace tags
            foreach ($arrTags as $strTag)
            {
                $strCondition = '';

                if (strncmp($strTag, '{if ', 4) === 0)
                {
                    preg_match_all('/\{if \'(.*)\'(.*?)\'(.*)\'\}/i', $strTag, $arrCondition);

                    if (count($arrCondition) !== 4)
                    {
                        throw new \Exception("Error condition-string [" . $strTag . "] in Trilobit\IfthenelseBundle\HookOutputFrontendTemplate::replaceConditionalTags()");
                    }

                    $token    = self::searchReplace($arrCondition[1][0]);
                    $operator = self::searchReplace($arrCondition[2][0], false);
                    $value    = self::searchReplace($arrCondition[3][0]);

                    if (!in_array($operator, $arrOperations))
                    {
                        throw new \Exception("Error condition (if-operator [" . $operator . "]) in Trilobit\IfthenelseBundle\HookOutputFrontendTemplate::replaceConditionalTags()");
                    }

                    $strCondition = '<?php if (\'' . $token . '\' ' . $operator . ' \'' . $value . '\'): ?>';
                    $blnEval = true;
                }
                elseif (strncmp($strTag, '{elseif ', 8) === 0)
                {
                    preg_match_all('/\{elseif \'(.*)\'(.*?)\'(.*)\'\}/i', $strTag, $arrCondition);

                    if (count($arrCondition) !== 4)
                    {
                        throw new \Exception("Error condition-string [" . $strTag . "] in Trilobit\IfthenelseBundle\HookOutputFrontendTemplate::replaceConditionalTags()");
                    }

                    $token    = self::searchReplace($arrCondition[1][0]);
                    $operator = self::searchReplace($arrCondition[2][0], false);
                    $value    = self::searchReplace($arrCondition[3][0]);

                    if (!in_array($operator, $arrOperations))
                    {
                        throw new \Exception("Error condition (elseif-operator [" . $operator . "]) in Trilobit\IfthenelseBundle\HookOutputFrontendTemplate::replaceConditionalTags()");
                    }

                    $strCondition = '<?php elseif (\'' . $token . '\' ' . $operator . ' \'' . $value . '\'): ?>';
                    $blnEval = true;
                }
                elseif (strncmp($strTag, '{else}', 6) === 0)
                {
                    $strCondition = '<?php else: ?>';
                    $blnEval = true;
                }
                elseif (strncmp($strTag, '{endif}', 7) === 0)
                {
                    $strCondition = '<?php endif; ?>';
                    $blnEval = true;
                }


                if ($strCondition !== '')
                {
                    $strReturn .= $strCondition;
                }
                else
                {
                    $strReturn .= $strTag;
                }
            }

            $strBuffer = $strReturn;

            if ($blnEval)
            {
                ob_start();

                /*
                 * ACHTUNG!
                 *
                 * ohne das '?>' kommt es zu folgendem Fehler:
                 *
                 * - app.CRITICAL: An exception occurred.
                 *   {
                 *       "exception":
                 *       "[object] (Symfony\\Component\\Debug\\Exception\\FatalThrowableError(code: 0): Parse error: syntax error, unexpected end of file at /export/Daten/contao/other/gasq/contao4/www/vendor/trilobit-gmbh/contao-ifthenelse-bundle/src/Resources/contao/classes/HookOutputFrontendTemplate.php:184)"
                 *   } []
                 *
                 */
                $blnEval = eval('?>' . $strBuffer);
                $strBuffer = ob_get_contents();
                ob_end_clean();

                if ($blnEval ===  false)
                {
                    throw new \Exception("Error eval() in Trilobit\IfthenelseBundle\HookOutputFrontendTemplate::replaceConditionalTags()");
                }
            }
        }

        $strBuffer = str_replace(array('__BRCL__', '__BRCR__'), array('{{', '}}'), $strBuffer);

        return $strBuffer;
    }
}

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

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Trilobit\IfthenelseBundle\HookOutputFrontendTemplate', 'replaceConditionalTags');

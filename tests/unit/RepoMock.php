<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App;

use SebastianFeldmann\Git\Operator\Info;
use SebastianFeldmann\Git\Repository;

/**
 * Class to more closely mimic the repo behaviour
 *
 * Only the operators ar supposed to be replaced by mocks
 */
class RepoMock extends Repository
{
    /**
     * Info Operator Mock
     *
     * @var \SebastianFeldmann\Git\Operator\Info
     */
    private Info $infoOperator;

    /**
     * Overwrite the original constructor to not do any validation at all
     */
    public function __construct()
    {
    }

    /**
     * Set info operator mock
     *
     * @param  \SebastianFeldmann\Git\Operator\Info $op
     * @return void
     */
    public function setInfoOperator(Info $op): void
    {
        $this->infoOperator = $op;
    }

    /**
     * Return the operator mock
     *
     * @return \SebastianFeldmann\Git\Operator\Info
     */
    public function getInfoOperator(): Info
    {
        return $this->infoOperator;
    }
}

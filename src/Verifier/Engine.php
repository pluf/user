<?php
/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Pluf\User\Verifier;

use JsonSerializable;
use Pluf\User\Verification;

/**
 * Defines a general verifier engine.
 * Different verifier engines should impelement this class.
 *
 * @author hadi
 *        
 */
abstract class Engine implements JsonSerializable
{

    const ENGINE_PREFIX = 'verifier_engine_';

    /**
     *
     * @return string
     */
    public function getType()
    {
        $name = strtolower(get_class($this));
        // NOTE: hadi, 2019: all verifier backend sould be placed in the determined folder
        if (strpos($name, self::ENGINE_PREFIX) !== 0) {
            throw new EngineLoadException('VerifierEngine class must be placed in engine package.');
        }
        return substr($name, strlen(self::ENGINE_PREFIX));
    }

    /**
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->getType();
    }

    /**
     *
     * @return string
     */
    public abstract function getTitle(): string;

    /**
     *
     * @return string
     */
    public abstract function getDescription(): string;

    /**
     * Sends verification to its related account.
     *
     * This function should be overrided by implementors.
     *
     * @param Verification $verification
     * @return boolean true if sending verification is successful else false.
     */
    public function send(Verification $verification): bool
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     *
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        $coded = array(
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'symbol' => $this->getSymbol()
        );
        return $coded;
    }

    /**
     * Returns an array of parameters of the verifier engine
     *
     * Each verifier engine needs some parameters which should be provided by the creator.
     * This function returns these parameters as an array.
     *
     * The returned value of this function is a list of property descriptors.
     */
    public function getParameters()
    {
        $param = array(
            'id' => $this->getType(),
            'name' => $this->getType(),
            'type' => 'struct',
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => $this->getSymbol(),
            'children' => []
        );
        $general = $this->getGeneralParam();
        foreach ($general as $gp) {
            $param['children'][] = $gp;
        }

        $extra = $this->getExtraParam();
        foreach ($extra as $ep) {
            $param['children'][] = $ep;
        }
        return $param;
    }

    /**
     * Returns a list of the general parameters.
     *
     * @return
     *
     */
    public function getGeneralParam()
    {
        $params = array();
        $params[] = array(
            'name' => 'title',
            'type' => 'String',
            'unit' => 'none',
            'title' => 'title',
            'description' => 'beackend title',
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => 'title',
            'defaultValue' => 'no title',
            'validators' => [
                'NotNull',
                'NotEmpty'
            ]
        );
        $params[] = array(
            'name' => 'description',
            'type' => 'String',
            'unit' => 'none',
            'title' => 'description',
            'description' => 'beackend description',
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => 'title',
            'defaultValue' => 'description',
            'validators' => []
        );
        $params[] = array(
            'name' => 'symbol',
            'type' => 'String',
            'unit' => 'none',
            'title' => 'Symbol',
            'description' => 'beackend symbol',
            'editable' => true,
            'visible' => true,
            'priority' => 5,
            'symbol' => 'icon',
            'defaultValue' => '',
            'validators' => []
        );
        return $params;
    }

    /**
     * Returns a list of the extra parameters.
     *
     * This functions should be overrided by implementors which have some other parameters other than general parameters.
     *
     * @return array
     */
    public function getExtraParam()
    {
        return array();
    }
}
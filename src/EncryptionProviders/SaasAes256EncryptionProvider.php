<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Scaffolds\Saas\EncryptionProviders;

use Rhubarb\Crown\Encryption\Aes256EncryptionProvider;

class SaasAes256EncryptionProvider extends Aes256EncryptionProvider
{
    /**
     * Implement this function to return a key for encryption and decryption
     *
     * @param string $keySalt An optional string used to derive the key. Not all use cases will supply this.
     * @return string
     */
    protected function getEncryptionKey($keySalt = "")
    {
        return "<3C927#6" . $keySalt . "6n|o44}V0yo|4PWaF5'}s~";
    }
}
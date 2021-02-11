<?php

namespace Acms\Plugins\Form2FieldGroup;

class Hook
{
    /**
     * POSTモジュール処理前
     * $thisModuleのプロパティを参照・操作するなど
     *
     * @param \ACMS_POST $thisModule
     */
    public function afterPostFire($thisModule)
    {
        $moduleName = get_class($thisModule);
        
        if ( $moduleName !== 'ACMS_POST_Form_Submit' ) {
            return;
        }
        
        $step = $thisModule->Post->get('step');
        if ($step === 'repeated' || $step === 'forbidden') {
            return;
        }

        $formCode = $thisModule->Post->get('id');
        try {
            $engine = new Engine($formCode, $thisModule);
            $engine->send();
        } catch (\Exception $e) {
            userErrorLog('ACMS Warning: Form2FieldGroup plugin, ' . $e->getMessage());
        }
    }
}

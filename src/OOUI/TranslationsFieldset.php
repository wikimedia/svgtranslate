<?php

declare(strict_types = 1);

namespace App\OOUI;

use OOUI\FieldLayout;
use OOUI\FieldsetLayout;
use OOUI\TextInputWidget;

class TranslationsFieldset extends FieldsetLayout
{
    /** @var bool Whether controls should be initially disabled */
    private $disabled = false;

    /**
     * @param array $config Configuration options
     * @param FieldLayout[] $config['translations'] Translations to add.
     * @param FieldLayout[] $config['source_lang_code'] Source language code.
     * @param FieldLayout[] $config['target_lang_code'] Target language code.
     */
    public function __construct(array $config = [])
    {
        $currentFieldset = new FieldsetLayout();
        $fieldsets = [$currentFieldset];
        $prevTranslation = null;
        $this->disabled = $config['disabled'] ?? false;
        $sourceLang = $config['source_lang_code'];
        $targetLang = $config['target_lang_code'];

        foreach ($config['translations'] as $tspanId => $translation) {
            // Don't suggest to translate a nonexistent message
            if (!isset($translation[$sourceLang]['text'])) {
                continue;
            }

            $fieldValue = $translation[$targetLang]['text'] ?? '';
            $field = $this->getField($tspanId, $translation[$sourceLang]['text'], $fieldValue);
            if (!$field) {
                continue;
            }
            // Start a new fieldset if the current translation's parent is different to the previous's.
            if ($prevTranslation
                && $prevTranslation['fallback']['data-parent'] !== $translation['fallback']['data-parent']) {
                $currentFieldset = new FieldsetLayout();
                $fieldsets[] = $currentFieldset;
            }
            $currentFieldset->addItems([$field]);
            $prevTranslation = $translation;
        }
        parent::__construct(['items' => $fieldsets]);
    }

    /**
     * Get a field for a single translation string.
     * @param string $tspanId The field name.
     * @param string $label The field label.
     * @param string $value The field value.
     * @return FieldLayout|bool The field, or false if the label is empty.
     */
    protected function getField(string $tspanId, string $label, string $value)
    {
        // Do not display translations that are only white-space. https://stackoverflow.com/a/4167053/99667
        // @TODO SvgFile should probably be handling this for us.
        $whitespacePattern = '/^[\pZ\pC]+|[\pZ\pC]+$/u';
        $sourceLabel = preg_replace($whitespacePattern, '', $label);
        if ('' === $sourceLabel) {
            return false;
        }
        $inputWidget = new TextInputWidget([
            'name' => $tspanId,
            'value' => $value,
            'data' => ['tspan-id' => $tspanId],
        ]);
        $inputWidget->setDisabled($this->disabled);
        return new FieldLayout($inputWidget, ['label' => $sourceLabel, 'infusable' => true]);
    }
}

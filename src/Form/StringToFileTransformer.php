<?php
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class StringToFileTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // transform the file instance to a string value
        return $value;
    }

    public function reverseTransform($value)
    {
        // transform the string value to a file instance
        return new File($value);
    }
}

$builder->add('image', FileType::class, [
    'data_class' => File::class,
    'model_transformer' => new StringToFileTransformer(),
]);

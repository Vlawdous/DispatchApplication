<?php

namespace Application\Controllers;

trait PostTrait
{
    private function isPost(string ...$items): bool
    {
        if (empty($items)) {
            return (!empty($_POST));
        } else {
            foreach ($items as $item) {
                if (!isset($_POST[$item])) {
                    return false;
                }
            }
            return true;
        }
    }
    private function getPost(string ...$items): array
    {
        $result = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                if (isset($_POST[$item])) {
                    if (is_array($_POST[$item])) {
                        foreach ($_POST[$item] as $key => $value) {
                            $result[$item][$key] = htmlspecialchars($value);
                        }
                    } else {
                        $result[$item] = htmlspecialchars($_POST[$item]);
                    }
                } else {
                    $result[$item] = null;
                }
            }
        } else {
            $result = $_POST;
        }
        return $result;
    }
}

<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

/**
 * Class EsParamManager.
 */
class EsParamManager implements ParamManager
{
    public function stripNulls(array $params, $toLower = true, $escape = true, array $types = [])
    {
        foreach ($params as $key => $val) {
            if ($val === null || $val == 'null') {
                unset($params[$key]);
                continue;
            }

            $val = rawurldecode($val);
            $params[$key] = $val;

            if ($toLower) {
                $params[$key] = strtolower($val);
            }
            $newKey = str_replace('_', '.', $key);
            if ($newKey != $key) {
                $params[$newKey] = $params[$key];
                unset($params[$key]);
                $key = $newKey;
            }
            if ($escape) {
                $params[$key] = static::escapeString($params[$key]);
            }
            if (isset($types[$key])) {
                $params[$key] = [
                    'type' => $types[$key],
                    'value' => $params[$key],
                ];
            }
        }

        return $params;
    }

    protected function escapeString($string)
    {
        $chars = array('\\',  '/', '+', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', "'", '~', '?', ':');
        foreach ($chars as $ch) {
            $string = str_replace($ch, '\\'.$ch, $string);
        }
        $string = str_replace('/', '\/', $string);

        return $string;
    }
}

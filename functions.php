<?php

/**
 * Return the depth of an array
 * Source: https://stackoverflow.com/a/263621
 */
function array_depth(array $array) :int {
  $max_indentation = 1;

  $array_str = print_r($array, true);
  $lines = explode("\n", $array_str);

  foreach ($lines as $line) {
    $indentation = (strlen($line) - strlen(ltrim($line))) / 4;

    if ($indentation > $max_indentation) {
      $max_indentation = $indentation;
    }
  }

  return ceil(($max_indentation - 1) / 2) + 1;
}

function processRecipientChoices(array $recipientChoices) :array {
  $depth = array_depth($recipientChoices);

  $choices = [
    'addresses' => [],
    'users' => [],
    'domains' => []
  ];

  $usersEqual = true;
  if ($depth == 1) {
    $choices['addresses'] = $recipientChoices;
  } elseif ($depth == 2) {
    if (array_key_exists('domains', $recipientChoices) && array_key_exists('users', $recipientChoices)) {
      $choices['domains'] = $recipientChoices['domains'];
      $choices['users'] = $recipientChoices['users'];
      foreach ($choices['domains'] as $domain) {
        foreach ($choices['users'] as $user) {
          $choices['addresses'][] = $user . '@' . $domain;
        }
      }
    } else {
      $choices['domains'] = array_keys($recipientChoices);
      foreach ($choices['domains'] as $domain)  {
        foreach ($choices['domains'] as $domain2)  {
          if ($recipientChoices[$domain] != $recipientChoices[$domain2]) {
            $usersEqual = false;
            $choices['users'] = [];
            break 2;
          } else {
            $choices['users'] = $recipientChoices[$domain];
          }
        }

        foreach ($choices['domains'] as $domain) {
          foreach($recipientChoices[$domain] as $user) {
            if (!empty($domain)) {
              $choices['addresses'][] = $user . '@' . $domain;
            } else {
              $choices['addresses'][] = $user;
            }
          }
        }
      }
    }

  } else {
    throw new Exception("There are to many levels for custom recipient choices available. Maximum is 2", 1);
  }


  return $choices;
}

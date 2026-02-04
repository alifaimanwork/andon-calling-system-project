<?php

declare(strict_types=1);

namespace App\Extras\Utils;

use Illuminate\Support\Facades\Session;

class ToastHelper
{
    private static function createToast($message, $title = null, $context = 'success', $dismissable = true, $options = null)
    {
        $toast = new \stdClass();
        $toast->title = $title;
        $toast->message = $message;
        $toast->context = $context;
        $toast->dismissable = $dismissable;
        $toast->options = $options;


        return $toast;
    }
    public static function generateToastOptions($animation = true, $autohide = true, $delay = 5000)
    {
        $options = new \stdClass();
        $options->animation = $animation;
        $options->autohide = $autohide;
        $options->delay = $delay;

        return $options;
    }
    public static function addToast($message, $title = null, $context = 'success', $dismissable = true, $options = null)
    {
        $toasts = Session::get('toast_alerts', []);
        $toasts[] = ToastHelper::createToast($message, $title, $context, $dismissable, $options);
        Session::flash('toast_alerts', $toasts);
    }
    public static function getToasts()
    {
        return Session::get('toast_alerts', []);
    }
    public static function clearToasts()
    {
        Session::forget('toast_alerts');
    }
}

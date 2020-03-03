<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class UtilController extends Controller
{
    public function commands()
    {
        Artisan::call('list', ['--format' => 'json']);
        $artisan = json_decode(Artisan::output());

        Artisan::call('list');

        $output = '<pre>' .
            $this->removeUnnecesaryOptions(Artisan::output()) .
            '</pre>';

        foreach ($artisan->commands as $index => $command) {
            Artisan::call($command->name, ['--help']);

            $output .= '<p>&nbsp;</p><h3>' . ($index+1) . '. ' . $command->name . '</h3>
                <pre>' .
                $this->removeUnnecesaryOptions(Artisan::output()) .
                '</pre>';
        }

        return $output;
    }

    /**
     * @param string $output
     * @return string
     */
    protected function removeUnnecesaryOptions(string $output): string
    {
        return trim(
            preg_replace(
                [
                    '/[\r\n](\s)*-h.*/',
                    '/[\r\n](\s)*-q.*/',
                    '/[\r\n](\s)*-V.*/',
                    '/[\r\n](\s)*--ansi.*/',
                    '/[\r\n](\s)*--no-ansi.*/',
                    '/[\r\n](\s)*-n.*/',
                    '/[\r\n](\s)*--env.*/',
                    '/[\r\n](\s)*-v.*/',
                    '/[\r\n]Options:$/',
                ],
                '',
                $output
            )
        );
    }
}

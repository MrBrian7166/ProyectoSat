protected $commands = [
    Commands\LimpiarArchivosTemporales::class,
];

protected function schedule(Schedule $schedule)
{
    $schedule->command('sat:limpiar-temporales')->daily();
}
$ErrorActionPreference = 'Stop'
$projectPath = Split-Path -Parent $PSScriptRoot
$publicPath = (Join-Path $projectPath 'backend/public').Replace('\', '/')
$apacheConfig = 'C:\xampp\apache\conf\extra\httpd-vhosts.conf'
$startMarker = '# BEGIN TRANSPORTES MERIDA'
$endMarker = '# END TRANSPORTES MERIDA'
$block = @"
$startMarker
Alias /transportes "$publicPath"
<Directory "$publicPath">
    Options FollowSymLinks
    AllowOverride All
    DirectoryIndex index.php
    Require local
</Directory>
$endMarker
"@
$original = [System.IO.File]::ReadAllText($apacheConfig)
$pattern = '(?ms)^# BEGIN TRANSPORTES MERIDA\r?\n.*?^# END TRANSPORTES MERIDA\r?\n?'
$updated = [regex]::Replace($original, $pattern, '')
$updated = $updated.TrimEnd() + "`r`n`r`n" + $block + "`r`n"
if ($original -eq $updated) { Write-Host 'Apache ya esta configurado.'; exit 0 }
$backup = Join-Path $projectPath 'scripts/httpd-vhosts.before-transportes.conf'
if (!(Test-Path -LiteralPath $backup)) { [System.IO.File]::WriteAllText($backup, $original, [System.Text.UTF8Encoding]::new($false)) }
[System.IO.File]::WriteAllText($apacheConfig, $updated, [System.Text.UTF8Encoding]::new($false))
& 'C:\xampp\apache\bin\httpd.exe' -t
if ($LASTEXITCODE -ne 0) {
    [System.IO.File]::WriteAllText($apacheConfig, $original, [System.Text.UTF8Encoding]::new($false))
    throw 'Apache rechazo la configuracion. Se restauro el archivo original.'
}
Write-Host 'Configurado: http://localhost/transportes. Inicia o reinicia Apache en XAMPP.'

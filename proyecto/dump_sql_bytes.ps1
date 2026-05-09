$path = 'C:\xampp\htdocs\proyecto\sql\datos_prueba.sql'
$bytes = [System.IO.File]::ReadAllBytes($path)
$needle = [System.Text.Encoding]::UTF8.GetBytes('Matem')
for ($i = 0; $i -lt $bytes.Length - $needle.Length; $i++) {
    $match = $true
    for ($j = 0; $j -lt $needle.Length; $j++) {
        if ($bytes[$i+$j] -ne $needle[$j]) { $match = $false; break }
    }
    if ($match) {
        $slice = $bytes[$i..($i+30)]
        Write-Host ([BitConverter]::ToString($slice))
        break
    }
}
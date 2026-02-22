param(
    [string]$AccessToken = $env:DIGITALOCEAN_ACCESS_TOKEN,
    [string]$RegistryName = "",
    [string]$KubernetesCluster = "",
    [int]$RegistryLoginTtlSeconds = 1800
)

$ErrorActionPreference = "Stop"

function Assert-CommandExists {
    param([string]$CommandName)

    if (-not (Get-Command $CommandName -ErrorAction SilentlyContinue)) {
        throw "Required command '$CommandName' was not found in PATH."
    }
}

Assert-CommandExists "doctl"

if ([string]::IsNullOrWhiteSpace($AccessToken)) {
    throw "DigitalOcean access token is missing. Set DIGITALOCEAN_ACCESS_TOKEN or pass -AccessToken."
}

Write-Host "Authenticating doctl..."
doctl auth init -t $AccessToken | Out-Null

Write-Host "Validating account access..."
doctl account get | Out-Null
Write-Host "DigitalOcean account connection OK."

if (-not [string]::IsNullOrWhiteSpace($RegistryName)) {
    Assert-CommandExists "docker"
    Write-Host "Logging Docker into DigitalOcean registry '$RegistryName'..."
    doctl registry login --expiry-seconds $RegistryLoginTtlSeconds | Out-Null
    Write-Host "Registry login OK."
}

if (-not [string]::IsNullOrWhiteSpace($KubernetesCluster)) {
    Write-Host "Fetching kubeconfig for cluster '$KubernetesCluster'..."
    doctl kubernetes cluster kubeconfig save $KubernetesCluster | Out-Null
    Write-Host "Kubeconfig updated."
}

Write-Host "DigitalOcean connection complete."

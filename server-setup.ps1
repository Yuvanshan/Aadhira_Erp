# ==============================================================================
# Aadhira ERP - Windows Cloud Server Setup Script
# ==============================================================================
# This script configures a Windows PC / Windows Server to host Aadhira ERP securely.
# It enables the built-in OpenSSH Server (for GitHub Actions CD deployment),
# sets PowerShell as the default SSH shell, and configures the Windows Firewall.
# ==============================================================================

# Ensure running as Administrator
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Error "ERROR: Please run this script in a PowerShell console opened as Administrator."
    exit 1
}

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host " Starting Windows Server Setup for Aadhira ERP" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# 1. Install and Enable OpenSSH Server
Write-Host ">>> Step 1: Installing OpenSSH Server capability..." -ForegroundColor Green
$sshStatus = Get-WindowsCapability -Online | Where-Object Name -like 'OpenSSH.Server*'

if ($sshStatus.State -ne 'Installed') {
    Add-WindowsCapability -Online -Name $sshStatus.Name
    Write-Host "OpenSSH Server capability installed." -ForegroundColor Green
} else {
    Write-Host "OpenSSH Server is already installed." -ForegroundColor Yellow
}

# Start SSH service and set to Automatic startup
Write-Host ">>> Starting SSH Daemon..." -ForegroundColor Green
Start-Service sshd
Set-Service -Name sshd -StartupType 'Automatic'

# 2. Configure default SSH shell to PowerShell
Write-Host ">>> Step 2: Setting PowerShell as the default SSH shell..." -ForegroundColor Green
$registryPath = "HKLM:\SOFTWARE\OpenSSH"
if (-not (Test-Path $registryPath)) {
    New-Item -Path "HKLM:\SOFTWARE" -Name "OpenSSH" | Out-Null
}
New-ItemProperty -Path $registryPath -Name "DefaultShell" -Value "C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe" -PropertyType String -Force | Out-Null

# 3. Configure Windows Defender Firewall Rules
Write-Host ">>> Step 3: Configuring Firewall Rules..." -ForegroundColor Green

# Remove any existing rules first to avoid duplicates
Remove-NetFirewallRule -DisplayName "Aadhira ERP HTTP" -ErrorAction SilentlyContinue
Remove-NetFirewallRule -DisplayName "Aadhira ERP HTTPS" -ErrorAction SilentlyContinue
Remove-NetFirewallRule -DisplayName "Aadhira ERP SSH" -ErrorAction SilentlyContinue

# Create rules for HTTP, HTTPS, and SSH ports
New-NetFirewallRule -DisplayName "Aadhira ERP HTTP" -Direction Inbound -LocalPort 80 -Protocol TCP -Action Allow -Description "Allow HTTP traffic for ERP web interface" | Out-Null
New-NetFirewallRule -DisplayName "Aadhira ERP HTTPS" -Direction Inbound -LocalPort 443 -Protocol TCP -Action Allow -Description "Allow HTTPS traffic for secure ERP API" | Out-Null
New-NetFirewallRule -DisplayName "Aadhira ERP SSH" -Direction Inbound -LocalPort 22 -Protocol TCP -Action Allow -Description "Allow SSH traffic for GitHub Action CD deployment" | Out-Null

Write-Host "Firewall configured: Ports 80 (HTTP), 443 (HTTPS), and 22 (SSH) are allowed." -ForegroundColor Green
Write-Host "All other ports (including database ports 3306/3307) remain blocked from outside access." -ForegroundColor Yellow

# 4. Prepare directory
Write-Host ">>> Step 4: Creating project directory..." -ForegroundColor Green
$targetDir = "C:\Aadhira_erp_v_1.0"
if (-not (Test-Path $targetDir)) {
    New-Item -Path "C:\" -Name "Aadhira_erp_v_1.0" -ItemType Directory | Out-Null
}

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host " Setup Completed Successfully!" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Next Steps:"
Write-Host "1. Install 'Docker Desktop for Windows' on this server (with WSL2 backend enabled)."
Write-Host "2. Clone your GitHub repository into the target directory:"
Write-Host "   git clone https://github.com/Yuvanshan/Aadhira_Erp.git C:\Aadhira_erp_v_1.0"
Write-Host "3. Point your public domain/subdomain to this PC's public IP address."
Write-Host "4. Add your GitHub Actions repository secrets (SSH_HOST, SSH_USER, SSH_PRIVATE_KEY) to trigger auto-deployments."
Write-Host ""

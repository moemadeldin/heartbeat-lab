<x-mail::message>
# {{ $daysUntilExpiry <= 0 ? '🔴 SSL Certificate Expired' : '🟡 SSL Certificate Expiring Soon' }}

**{{ $site->name }}** ({{ $site->url }}) has an SSL certificate issue.

@if ($daysUntilExpiry <= 0)
Your SSL certificate has **expired**. Visitors may see security warnings.
@elseif ($daysUntilExpiry <= 7)
Your SSL certificate will expire in **{{ $daysUntilExpiry }} days**. Renew it immediately.
@else
Your SSL certificate will expire in **{{ $daysUntilExpiry }} days**.
@endif

---

### Certificate Details

| | |
|---|---|
| **Site** | {{ $site->name }} |
| **URL** | {{ $site->url }} |
| **Expires** | {{ $expiresAt->format('F j, Y') }} |
| **Days Left** | {{ $daysUntilExpiry <= 0 ? 'Expired' : $daysUntilExpiry }} |
| **Issuer** | {{ $site->ssl_issuer ?? 'N/A' }} |

<x-mail::button :url="$dashboardUrl">
Go to Dashboard
</x-mail::button>

---

*This is an automated alert from Heartbeat Lab.*
</x-mail::message>

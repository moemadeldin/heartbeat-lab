<x-mail::message>
# {{ $isOnline ? '🟢 Site is Back Online' : '🔴 Site is Down' }}

**{{ $site->name }}** ({{ $site->url }}) has changed status.

@if ($isOnline)
Your site is now **online** and responding normally.
@else
Your site is currently **unreachable** or returning an error.
@endif

---

### Check Details

| | |
|---|---|
| **Status** | {{ $isOnline ? 'Online' : 'Offline' }} |
| **Status Code** | {{ $statusText }} |
| **Response Time** | {{ $responseTime !== null ? number_format($responseTime, 0) . ' ms' : 'N/A' }} |
| **Last Checked** | {{ $checkedAt?->format('F j, Y g:i A T') ?? 'N/A' }} |

<x-mail::button :url="$dashboardUrl">
Go to Dashboard
</x-mail::button>

---

*This is an automated alert from Heartbeat Lab.*
</x-mail::message>

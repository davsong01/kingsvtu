@extends('sneat.layouts.app')

@section('title', 'Email Management')

@section('content')
    @php
        $isPending = $mode === 'pending';
        $summary = [
            ['label' => 'Total emails', 'value' => number_format($totalEmails), 'tone' => 'blue'],
            ['label' => 'Sent', 'value' => number_format($sentCount), 'tone' => 'green'],
            ['label' => 'Pending', 'value' => number_format($pendingCount), 'tone' => 'amber'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Email Management</span>
                    <h1>{{ $isPending ? 'Pending Emails' : 'Sent Emails' }}</h1>
                    <p>Track outgoing mail, review content, and manage queued messages from a clean admin workspace.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="{{ route('emails.index') }}" class="btn {{ !$isPending ? 'btn-admin-submit' : 'gateway-action' }}">Sent Emails</a>
                    <a href="{{ route('emails.pending') }}" class="btn {{ $isPending ? 'btn-admin-submit' : 'gateway-action' }}">Pending Emails</a>
                    @if(!$isPending)
                        <a href="{{ route('emails.sweep') }}" class="gateway-action gateway-action--danger" onclick="return confirm('You are about to clear sent emails');">
                            Clear Sent Emails
                        </a>
                    @endif
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                @foreach($summary as $card)
                    <div class="col-md-4">
                        <div class="admin-stat-card admin-stat-card--{{ $card['tone'] }}">
                            <div class="admin-stat-card__label">{{ $card['label'] }}</div>
                            <div class="admin-stat-card__value">{{ $card['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="gateway-card card">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div>
                        <h3>{{ $isPending ? 'Pending queue' : 'Mail log' }}</h3>
                        <p>{{ $isPending ? 'Queued emails that are ready to edit or send.' : 'Delivered email records with quick resend and review actions.' }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="gateway-badge gateway-badge--active">{{ number_format($sentCount) }} sent</span>
                        <span class="gateway-badge gateway-badge--warning">{{ number_format($pendingCount) }} pending</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="gateway-helper mb-3">
                        Showing {{ $emails->firstItem() ?? 0 }} to {{ $emails->lastItem() ?? 0 }} of {{ $emails->total() }} emails
                    </div>

                    <div class="table-responsive">
                        <table class="table gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Recipient</th>
                                    <th>Subject</th>
                                    <th>Preview</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emails as $mail)
                                    @php
                                        $serial = ($emails->firstItem() ?? 0) + $loop->index;
                                        $status = strtolower((string) $mail->status);
                                        $badgeClass = match ($status) {
                                            'sent' => 'gateway-badge--active',
                                            'pending' => 'gateway-badge--warning',
                                            default => 'gateway-badge--danger',
                                        };
                                        $rawContent = base64_encode($mail->content ?? '');
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold text-secondary">{{ $serial }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $mail->recipient }}</div>
                                            <div class="gateway-helper">Created: {{ $mail->created_at }}</div>
                                            @if($mail->sent_at)
                                                <div class="gateway-helper text-success">Sent: {{ $mail->sent_at }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $mail->subject }}</div>
                                        </td>
                                        <td>
                                            <div class="gateway-helper" style="max-width: 26rem;">
                                                {{ \Illuminate\Support\Str::limit(strip_tags((string) $mail->content), 130) }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="gateway-badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                        </td>
                                        <td>{{ $mail->created_at }}</td>
                                        <td class="text-end">
                                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                <button
                                                    type="button"
                                                    class="gateway-action email-log-trigger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#emailLogModal"
                                                    data-mode="{{ $isPending ? 'edit' : 'view' }}"
                                                    data-action="{{ $isPending ? route('emails.update', $mail->id) : '' }}"
                                                    data-content="{{ $rawContent }}"
                                                    data-recipient="{{ $mail->recipient }}"
                                                    data-subject="{{ $mail->subject }}"
                                                >
                                                    {{ $isPending ? 'Edit' : 'View' }}
                                                </button>

                                                @if($status === 'sent')
                                                    <a class="gateway-action" href="{{ route('emails.resend', $mail->id) }}">Resend</a>
                                                    <a class="gateway-action gateway-action--danger" onclick="return confirm('You are about to delete a logged email')" href="{{ route('emails.destroy', $mail->id) }}">Delete</a>
                                                @else
                                                    <a
                                                        class="btn btn-info btn-sm text-white"
                                                        style="background:#0dcaf0;border-color:#0dcaf0;color:#fff !important;"
                                                        onclick="return confirm('Send this pending email now?');"
                                                        href="{{ route('emails-send', $mail->id) }}"
                                                    >
                                                        Send now
                                                    </a>
                                                    <a class="gateway-action gateway-action--danger" onclick="return confirm('You are about to delete this pending email')" href="{{ route('emails.destroy', $mail->id) }}">Delete</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="alert alert-light border mb-0">No emails found.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        {{ $emails->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="emailLogModal" tabindex="-1" aria-labelledby="emailLogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="emailLogModalLabel">View Email</h5>
                        <div class="gateway-helper" id="emailLogModalMeta">Preview email content</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="email-log-form" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <div class="modern-admin-card p-3 h-100">
                                    <div class="fw-semibold mb-2">Email details</div>
                                    <div class="mb-3">
                                        <div class="gateway-helper text-uppercase fw-semibold">Recipient</div>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }} email-log-recipient" value="-" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <div class="gateway-helper text-uppercase fw-semibold">Subject</div>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }} email-log-subject" value="-" readonly>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="modern-admin-card p-3 h-100">
                                    <div class="admin-note-section">
                                        <div class="admin-note-section__heading">
                                            <span>Message</span>
                                            <small>Editable rich text</small>
                                        </div>
                                        <div class="admin-editor">
                                            <div class="admin-editor__toolbar">
                                                <button type="button" class="btn btn-sm btn-light" data-editor-cmd="bold"><strong>B</strong></button>
                                                <button type="button" class="btn btn-sm btn-light" data-editor-cmd="italic"><em>I</em></button>
                                                <button type="button" class="btn btn-sm btn-light" data-editor-cmd="underline"><u>U</u></button>
                                                <button type="button" class="btn btn-sm btn-light" data-editor-cmd="insertUnorderedList">List</button>
                                                <button type="button" class="btn btn-sm btn-light" data-editor-cmd="removeFormat">Clear</button>
                                            </div>
                                            <div id="email-message-editor" class="admin-editor__body" contenteditable="true"></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="message" id="email-log-message">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="gateway-action" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-admin-submit email-log-update-btn">Update email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        (function () {
            function decodeEmailContent(encodedContent) {
                if (!encodedContent) {
                    return '';
                }

                try {
                    return decodeURIComponent(escape(atob(encodedContent)));
                } catch (error) {
                    return atob(encodedContent);
                }
            }

            function execEditorCommand(command) {
                const editor = document.getElementById('email-message-editor');
                if (!editor) {
                    return;
                }

                editor.focus();
                document.execCommand(command, false, null);
            }

            document.querySelectorAll('[data-editor-cmd]').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    execEditorCommand(this.getAttribute('data-editor-cmd'));
                });
            });

            function setEditorHtml(html) {
                const editor = document.getElementById('email-message-editor');
                if (editor) {
                    editor.innerHTML = html || '';
                }
            }

            $(document).on('click', '.email-log-trigger', function () {
                const $btn = $(this);
                const mode = $btn.data('mode') || 'view';
                const action = $btn.data('action') || '';
                const recipient = $btn.data('recipient') || '-';
                const subject = $btn.data('subject') || '-';
                const encodedContent = $btn.attr('data-content') || '';
                const content = decodeEmailContent(encodedContent);

                $('#emailLogModalLabel').text(mode === 'edit' ? 'Edit Email' : 'View Email');
                $('#emailLogModalMeta').text(mode === 'edit' ? 'Update queued email content' : 'Review sent email content');
                $('.email-log-recipient').val(recipient);
                $('.email-log-subject').val(subject);
                $('.email-log-mode').val(mode === 'edit' ? 'Editable draft' : 'Read only');
                setEditorHtml(content);
                $('#email-message-editor').attr('contenteditable', mode === 'edit' ? 'true' : 'false');
                $('#email-log-message').val(content || '');

                const $form = $('.email-log-form');
                $form.attr('action', action);
                $('.email-log-update-btn').toggle(mode === 'edit');
            });

            $('#emailLogModal').on('hidden.bs.modal', function () {
                $('.email-log-form').attr('action', '');
                $('.email-log-update-btn').show();
                $('.email-log-recipient').val('-');
                $('.email-log-subject').val('-');
                $('.email-log-mode').val('-');
                $('#email-log-message').val('');
                setEditorHtml('');
                $('#email-message-editor').attr('contenteditable', 'false');
            });

            $('.email-log-form').on('submit', function () {
                $('#email-log-message').val($('#email-message-editor').html() || '');
            });
        })();
    </script>
@endsection

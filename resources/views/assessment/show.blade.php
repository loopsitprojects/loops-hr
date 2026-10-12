<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Task | Loops HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        .bg-gradient { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .animate-shake {
            animation: shake 0.35s ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="bg-gradient h-64 w-full absolute top-0 z-0"></div>
    
    <div class="relative z-10 max-w-4xl mx-auto px-4 py-12">
        <div class="flex justify-between items-center mb-8">
            <div class="text-white">
                <h1 class="text-3xl font-black tracking-tight">Loops <span class="text-teal-400">Assessment</span></h1>
                <p class="text-slate-400 text-sm uppercase tracking-widest mt-1">Recruitment Portal</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-full px-4 py-2 border border-white/20">
                <span class="text-white text-xs font-bold uppercase tracking-wider">Candidate: {{ $assessment->candidate->name }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-500 text-white p-4 rounded-2xl mb-6 shadow-lg flex items-center gap-3">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-500 text-white p-4 rounded-2xl mb-6 shadow-lg">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="font-bold text-sm">Please correct the following:</span>
                </div>
                <ul class="list-disc list-inside text-xs pl-2 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
            <div class="p-8 border-b border-slate-50">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-teal-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">{{ $assessment->test->name }}</h2>
                        <p class="text-slate-500 text-xs uppercase tracking-widest font-semibold">{{ $assessment->test->subject }}</p>
                    </div>
                </div>

                <div class="prose prose-slate max-w-none text-slate-600 mb-8 border-b border-slate-50 pb-8">
                    <h3 class="text-sm font-black uppercase text-slate-400 tracking-widest mb-4">Task Instructions</h3>
                    <div class="whitespace-pre-wrap text-sm leading-relaxed">{!! nl2br(e($assessment->test->content)) !!}</div>
                </div>

                @if($assessment->test->attachment_path)
                    <div class="mb-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="p-6 flex justify-between items-center group hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-teal-50 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Task Attachment</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Please download the file to view the task details.</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $assessment->test->attachment_path) }}" download class="px-4 py-2 bg-teal-50 text-teal-700 hover:bg-teal-100 rounded-xl text-xs font-bold uppercase tracking-widest transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download File
                            </a>
                        </div>
                    </div>
                @endif

                <div class="bg-teal-50 border border-teal-100 p-6 rounded-2xl flex items-center gap-4">
                    <div class="w-12 h-12 bg-teal-100 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-teal-900 font-bold">Ready to submit?</p>
                        <p class="text-teal-700 text-sm">Please provide the Google Drive link to your completed task below. Only Google Drive links are accepted. Please make sure the link access is shareable for evaluation.</p>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-slate-50/30">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Submit Google Drive Link
                </h3>

                @if($assessment->status == 'Submitted')
                    <div class="bg-teal-50 border border-teal-100 p-8 rounded-2xl text-center">
                        <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h4 class="text-teal-900 font-bold text-lg">Assessment Submitted</h4>
                        <p class="text-teal-700 text-sm mt-1">Thank you! Your assessment has been received and is under review.</p>
                        @if($assessment->submission_link)
                            <div class="mt-4 p-4 bg-white rounded-xl border border-teal-200 text-left max-w-xl mx-auto shadow-sm">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Submitted Google Drive Link</span>
                                <a href="{{ $assessment->submission_link }}" target="_blank" rel="noopener noreferrer" class="text-sm text-teal-700 font-medium hover:text-teal-900 underline break-all flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    <span>{{ $assessment->submission_link }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <form id="assessmentForm" action="{{ route('assessment.submit', $assessment->token) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="submission_links" class="block text-xs font-black uppercase text-slate-400 tracking-widest">
                                    Google Drive Submission Link <span class="text-rose-500">*</span>
                                </label>
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 87.3 78" fill="none">
                                        <path d="M6.6 66.85l3.85 6.65c.8 1.4 1.9 2.5 3.2 3.3l17.3-30-7.05-12.2-17.3 30c0 1.5.4 2.9 1.2 4.15l-1.2-1.9z" fill="#0066DA"/>
                                        <path d="M43.65 25L26.35 25l7.05 12.2 17.3 0 7.05-12.2z" fill="#00AC47"/>
                                        <path d="M73.55 66.85c.8-1.4 1.2-2.9 1.2-4.45 0-1.5-.4-3-1.2-4.45l-17.3-30-7.05 12.2 17.3 30c1.4-.8 2.5-1.9 3.2-3.3h3.8z" fill="#EA4335"/>
                                        <path d="M43.65 25L50.7 12.8c-1.4-.8-2.9-1.2-4.5-1.2h-17.8c-1.6 0-3.1.4-4.5 1.2L31 25h12.65z" fill="#00832D"/>
                                        <path d="M57.8 76.8c1.3-.8 2.4-1.9 3.2-3.3l3.85-6.65 1.2 1.9c-.8 1.4-1.9 2.5-3.2 3.3l-17.3-30-7.05 12.2 17.3 30c.7-.4 1.4-.8 2-1.45z" fill="#2684FC"/>
                                        <path d="M26.35 25L9.05 55c-.8 1.4-1.2 2.9-1.2 4.45 0 1.5.4 3 1.2 4.45l17.3-30 0-8.9z" fill="#FFBA00"/>
                                    </svg>
                                    Google Drive Only
                                </span>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                                <input type="url" 
                                    name="submission_links" 
                                    id="submission_links" 
                                    required
                                    value="{{ old('submission_links') }}"
                                    class="w-full bg-white border border-slate-200 rounded-2xl py-4 pl-12 pr-12 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition-all placeholder:text-slate-300 font-mono"
                                    placeholder="https://drive.google.com/drive/folders/... or https://drive.google.com/file/d/..."
                                    autocomplete="off">

                                <div id="validationIcon" class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none hidden">
                                </div>
                            </div>

                            <div id="validationMessage" class="mt-2 text-xs transition-all"></div>

                            @error('submission_links')
                                <p class="text-xs text-rose-500 font-semibold mt-1.5 flex items-center gap-1">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror

                            <div class="mt-3 bg-amber-50/70 border border-amber-200/60 rounded-xl p-3.5 flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    <strong class="font-bold">Required Sharing Setting:</strong> Please ensure your Google Drive file/folder sharing permission is set to <span class="font-semibold underline">"Anyone with the link can view"</span> so our evaluation team can review your submission.
                                </p>
                            </div>
                        </div>

                        <button type="button" 
                            id="submitBtn"
                            class="w-full bg-slate-900 hover:bg-teal-600 text-white font-bold py-4 rounded-2xl shadow-xl shadow-slate-900/10 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>Complete Submission</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
            
            <div class="p-6 bg-slate-900 text-center">
                <p class="text-slate-500 text-[10px] uppercase tracking-[0.2em]">Powered by Loops Integrated (Pvt) Ltd</p>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal Dialog -->
    <div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200" aria-modal="true" role="dialog">
        <div id="confirmModalCard" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8 border border-slate-100 transform scale-95 transition-transform duration-200">
            <!-- Modal Header -->
            <div class="flex items-start gap-4 mb-5">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Confirm Submission</h3>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mt-0.5">Google Drive Access Verification</p>
                </div>
            </div>

            <!-- Link Preview Box -->
            <div class="mb-5">
                <label class="block text-[11px] font-bold uppercase text-slate-400 tracking-wider mb-1.5">Submitted Google Drive Link</label>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2 overflow-hidden">
                    <svg class="w-4 h-4 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    <span id="modalPreviewLink" class="text-xs font-mono text-slate-700 truncate select-all"></span>
                </div>
            </div>

            <!-- Checkbox Checklist & Red Disclaimer -->
            <div class="space-y-3 mb-6 bg-slate-50/70 p-4 rounded-2xl border border-slate-200">
                <p class="text-[11px] font-bold uppercase text-slate-500 tracking-wider mb-1">Please confirm before submitting:</p>
                
                <label for="chk_general_access" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-white border border-transparent hover:border-slate-200 transition-colors cursor-pointer group">
                    <input type="checkbox" id="chk_general_access" class="w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500 focus:ring-2 cursor-pointer transition shrink-0 accent-teal-600">
                    <span class="text-xs text-slate-700 select-none group-hover:text-slate-900 font-medium">General access set to <strong>"Anyone with the link"</strong></span>
                </label>

                <label for="chk_viewer_access" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-white border border-transparent hover:border-slate-200 transition-colors cursor-pointer group">
                    <input type="checkbox" id="chk_viewer_access" class="w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500 focus:ring-2 cursor-pointer transition shrink-0 accent-teal-600">
                    <span class="text-xs text-slate-700 select-none group-hover:text-slate-900 font-medium">Permission level allows <strong>"Viewer"</strong> access</span>
                </label>

                <!-- Disclaimer in RED Color -->
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl flex items-start gap-2.5 text-xs text-red-600 mt-2">
                    <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="leading-relaxed"><strong class="font-bold">Disclaimer:</strong> If access is private, our evaluation cannot proceed.</span>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row gap-3">
                <button type="button" 
                    id="modalCancelBtn"
                    class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors">
                    Go Back & Check
                </button>
                <button type="button" 
                    id="modalConfirmBtn"
                    disabled
                    class="flex-1 px-4 py-3 bg-slate-300 text-slate-500 font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2 cursor-not-allowed">
                    <span>Yes, Confirm & Submit</span>
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('assessmentForm');
        if (!form) return;

        const input = document.getElementById('submission_links');
        const submitBtn = document.getElementById('submitBtn');
        const validationMessage = document.getElementById('validationMessage');
        const validationIcon = document.getElementById('validationIcon');

        const modal = document.getElementById('confirmModal');
        const modalCard = document.getElementById('confirmModalCard');
        const modalPreviewLink = document.getElementById('modalPreviewLink');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        const modalConfirmBtn = document.getElementById('modalConfirmBtn');
        const chkGeneralAccess = document.getElementById('chk_general_access');
        const chkViewerAccess = document.getElementById('chk_viewer_access');

        function updateModalConfirmState() {
            const isConfirmed = chkGeneralAccess && chkViewerAccess && chkGeneralAccess.checked && chkViewerAccess.checked;
            if (isConfirmed) {
                modalConfirmBtn.disabled = false;
                modalConfirmBtn.className = 'flex-1 px-4 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-teal-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer';
            } else {
                modalConfirmBtn.disabled = true;
                modalConfirmBtn.className = 'flex-1 px-4 py-3 bg-slate-300 text-slate-500 font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2 cursor-not-allowed';
            }
        }

        if (chkGeneralAccess) chkGeneralAccess.addEventListener('change', updateModalConfirmState);
        if (chkViewerAccess) chkViewerAccess.addEventListener('change', updateModalConfirmState);

        function normalizeUrl(value) {
            let trimmed = (value || '').trim();
            if (trimmed && !/^https?:\/\//i.test(trimmed)) {
                trimmed = 'https://' + trimmed;
            }
            return trimmed;
        }

        function checkGoogleDriveUrl(value) {
            if (!value || !value.trim()) {
                return { valid: false, empty: true, message: 'Please enter your Google Drive submission link.' };
            }
            
            const normalized = normalizeUrl(value);
            let parsed;
            try {
                parsed = new URL(normalized);
            } catch (e) {
                return { valid: false, empty: false, message: 'Please enter a valid URL (e.g., https://drive.google.com/...).' };
            }

            const host = (parsed.hostname || '').toLowerCase();
            const validHosts = ['drive.google.com', 'docs.google.com'];
            if (!validHosts.includes(host)) {
                return { 
                    valid: false, 
                    empty: false, 
                    message: 'Only Google Drive links (drive.google.com or docs.google.com) are accepted. Other links cannot be submitted.' 
                };
            }

            const path = parsed.pathname || '';
            const query = parsed.search || '';
            if ((path === '/' || path === '') && (!query || query.length < 4)) {
                return { 
                    valid: false, 
                    empty: false, 
                    message: 'Please provide the complete link to your specific Google Drive file or folder, not just the root domain.' 
                };
            }

            return { valid: true, empty: false, normalizedUrl: normalized };
        }

        function updateUiValidation(showErrors = false) {
            const val = input.value;
            const check = checkGoogleDriveUrl(val);

            if (check.empty) {
                input.classList.remove('border-emerald-500', 'border-rose-500', 'ring-2', 'ring-rose-500/20', 'ring-emerald-500/20');
                input.classList.add('border-slate-200');
                validationIcon.classList.add('hidden');
                validationIcon.innerHTML = '';
                if (showErrors) {
                    validationMessage.innerHTML = '<span class="text-rose-500 font-medium">⚠️ ' + check.message + '</span>';
                } else {
                    validationMessage.innerHTML = '';
                }
                return check;
            }

            if (check.valid) {
                input.classList.remove('border-rose-500', 'border-slate-200', 'ring-rose-500/20');
                input.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                validationIcon.classList.remove('hidden');
                validationIcon.innerHTML = `
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                `;
                validationMessage.innerHTML = '<span class="text-emerald-600 font-medium">✓ Valid Google Drive link recognized</span>';
            } else {
                input.classList.remove('border-emerald-500', 'border-slate-200', 'ring-emerald-500/20');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                validationIcon.classList.remove('hidden');
                validationIcon.innerHTML = `
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                `;
                validationMessage.innerHTML = '<span class="text-rose-500 font-medium">⚠️ ' + check.message + '</span>';
            }

            return check;
        }

        input.addEventListener('input', function () {
            updateUiValidation(false);
        });

        input.addEventListener('blur', function () {
            if (input.value.trim()) {
                input.value = normalizeUrl(input.value);
                updateUiValidation(true);
            }
        });

        function openModal(link) {
            modalPreviewLink.textContent = link;
            if (chkGeneralAccess) chkGeneralAccess.checked = false;
            if (chkViewerAccess) chkViewerAccess.checked = false;
            updateModalConfirmState();

            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                modalCard.classList.remove('scale-95');
                modalCard.classList.add('scale-100');
            });
        }

        function closeModal() {
            modal.classList.add('opacity-0');
            modalCard.classList.remove('scale-100');
            modalCard.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                input.focus();
            }, 150);
        }

        submitBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (input.value.trim()) {
                input.value = normalizeUrl(input.value);
            }
            const check = updateUiValidation(true);

            if (!check.valid) {
                input.focus();
                input.classList.add('animate-shake');
                setTimeout(() => input.classList.remove('animate-shake'), 400);
                return;
            }

            openModal(check.normalizedUrl);
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            submitBtn.click();
        });

        modalCancelBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        modalConfirmBtn.addEventListener('click', function () {
            if (!chkGeneralAccess.checked || !chkViewerAccess.checked) {
                return;
            }
            modalConfirmBtn.disabled = true;
            modalCancelBtn.disabled = true;
            modalConfirmBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Submitting Assessment...</span>
            `;

            input.value = normalizeUrl(input.value);
            HTMLFormElement.prototype.submit.call(form);
        });
    });
    </script>
</body>
</html>

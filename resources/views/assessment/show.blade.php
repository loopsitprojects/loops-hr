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
            <div class="bg-emerald-500 text-white p-4 rounded-2xl mb-6 shadow-lg animate-bounce">
                {{ session('success') }}
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
                    <div class="flex-1">
                        <p class="text-teal-900 font-bold">Ready to submit?</p>
                        <p class="text-teal-700 text-sm">Please provide the necessary links to your completed task (e.g., Google Drive, Canva, or Portfolio) below.</p>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-slate-50/30">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Submit Your Links
                </h3>

                @if($assessment->status == 'Submitted')
                    <div class="bg-teal-50 border border-teal-100 p-6 rounded-2xl text-center">
                        <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h4 class="text-teal-900 font-bold text-lg">Already Submitted</h4>
                        <p class="text-teal-700 text-sm mt-1">Thank you! Your assessment has been received and is under review.</p>
                    </div>
                @else
                    <form action="{{ route('assessment.submit', $assessment->token) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-400 tracking-widest mb-2">Submission Links (Google Drive, Canva, etc.)</label>
                            <textarea name="submission_links" rows="6" required
                                class="w-full bg-white border border-slate-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition-all placeholder:text-slate-300"
                                placeholder="Paste your Google Drive, Canva, or Portfolio links here..."></textarea>
                            <p class="text-[10px] text-slate-400 mt-2">Please ensure any external links are accessible to our recruitment team.</p>
                        </div>

                        <button type="submit" 
                            class="w-full bg-slate-900 hover:bg-teal-600 text-white font-bold py-4 rounded-2xl shadow-xl shadow-slate-900/10 transition-all active:scale-[0.98]">
                            Complete Submission
                        </button>
                    </form>
                @endif
            </div>
            
            <div class="p-6 bg-slate-900 text-center">
                <p class="text-slate-500 text-[10px] uppercase tracking-[0.2em]">Powered by Loops Integrated (Pvt) Ltd</p>
            </div>
        </div>
    </div>
</body>
</html>

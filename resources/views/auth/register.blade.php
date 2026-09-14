<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Daftar - SAP.HRIS</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="min-h-screen bg-slate-950 flex items-center justify-center p-4 text-white">
<main class="w-full max-w-lg rounded-2xl border border-slate-700 bg-slate-900 p-8 shadow-2xl">
<h1 class="text-2xl font-bold mb-1">Buat akun SAP.HRIS</h1><p class="text-slate-400 text-sm mb-6">Akun baru dibuat sebagai Mandor Kebun dan dapat dikelola admin.</p>
@if ($errors->any())<div class="mb-4 rounded-lg bg-red-900/40 p-3 text-sm text-red-200">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('register.submit') }}" class="space-y-4">@csrf
@foreach ([['name','Nama lengkap','text'],['username','Username','text'],['email','Email','email'],['area','Area kerja','text']] as [$name,$label,$type])<label class="block text-sm">{{ $label }}<input name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 p-3"></label>@endforeach
<label class="block text-sm">Password<input name="password" type="password" required minlength="8" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 p-3"></label>
<label class="block text-sm">Ulangi password<input name="password_confirmation" type="password" required minlength="8" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 p-3"></label>
<button class="w-full rounded-lg bg-blue-600 p-3 font-bold">Buat akun</button></form>
<p class="mt-5 text-center text-sm text-slate-400"><a class="text-cyan-400" href="{{ route('login') }}">Kembali ke login</a></p></main></body></html>
@extends('backend.master')

@section('title', 'My Account')

@push('style')
<style>
    .profile-header-banner {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border-radius: 24px;
        padding: 60px 40px;
        margin-bottom: 30px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(255, 71, 61, 0.2);
    }

    .profile-header-content { position: relative; z-index: 2; }
    .profile-header-banner h1 { font-weight: 800; font-size: 36px; letter-spacing: -1.5px; margin-bottom: 10px; }
    .profile-header-banner p { font-size: 16px; opacity: 0.9; font-weight: 500; }

    .banner-blob {
        position: absolute;
        width: 300px; height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        filter: blur(50px);
    }
    .blob-1 { top: -100px; right: -50px; }
    .blob-2 { bottom: -150px; left: -50px; }

    .profile-card {
        background: #fff;
        border-radius: 24px;
        padding: 40px;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(255, 71, 61, 0.05);
        height: 100%;
    }

    .section-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .section-title i { color: var(--primary); font-size: 18px; }

    .form-label { font-weight: 700; font-size: 13px; color: var(--text-muted); margin-bottom: 10px; }
    .form-control {
        border-radius: 16px;
        padding: 14px 20px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        transition: all 0.3s ease;
        font-weight: 600;
    }
    .form-control:focus {
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(255, 71, 61, 0.1);
    }

    .profile-img-container {
        width: 140px; height: 140px;
        border-radius: 30px;
        border: 4px solid #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.3s ease;
        position: relative;
    }
    .profile-img-container:hover { transform: scale(1.03); }
    .profile-img-container img { width: 100%; height: 100%; object-fit: cover; }
    .img-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.4);
        display: flex; align-items: center; justify-content: center;
        color: #fff; opacity: 0; transition: opacity 0.3s;
    }
    .profile-img-container:hover .img-overlay { opacity: 1; }

    .btn-save {
        padding: 16px 35px;
        font-size: 14px;
        font-weight: 800;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--primary) 0%, #FF7B73 100%);
        border: none;
        color: #fff;
        box-shadow: 0 10px 20px rgba(255, 71, 61, 0.3);
        transition: all 0.3s ease;
    }
    .btn-save:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(255, 71, 61, 0.4); }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- ── Header Banner ────────────────────────────────── --}}
    <div class="profile-header-banner">
        <div class="banner-blob blob-1"></div>
        <div class="banner-blob blob-2"></div>
        <div class="profile-header-content">
            <h1>Account Settings</h1>
            <p>Manage your professional profile and security preferences ✨</p>
        </div>
    </div>

    <form action="{{ route('backend.admin.profile.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            {{-- ── Left: General Info ────────────────────────── --}}
            <div class="col-lg-8">
                <div class="profile-card">
                    <h3 class="section-title"><i class="fas fa-user-circle"></i> General Information</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-12">
                            <div class="profile-img-section">
                                <label class="form-label">Profile Picture</label>
                                <div class="profile-img-container" onclick="document.getElementById('profile_image').click()">
                                    <img src="{{ asset($user->profile_image) }}" id="preview" onerror="this.src='{{ asset('assets/images/no-image.png') }}'">
                                    <div class="img-overlay"><i class="fas fa-camera"></i></div>
                                </div>
                                <input type="file" name="profile_image" id="profile_image" class="d-none" onchange="previewImage(this)" accept="image/*">
                                <p class="text-muted small">Click the image to upload a new one. Best size: 400x400px.</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="opacity: 0.1;">

                    <h3 class="section-title"><i class="fas fa-lock"></i> Update Password</h3>
                    <p class="text-muted small mb-4">Leave these fields blank if you don't want to change your password.</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" placeholder="••••••••">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" placeholder="••••••••">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="new_password_confirmation" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-save">
                            Save Changes <i class="fas fa-magic ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Right: Stats/Info ────────────────────────── --}}
            <div class="col-lg-4">
                <div class="profile-card" style="background: #f8fafc; border: none;">
                    <h3 class="section-title"><i class="fas fa-bolt"></i> Quick Stats</h3>
                    
                    <div class="mb-4">
                        <div class="p-4 bg-white rounded-4 mb-3 shadow-sm d-flex align-items-center gap-3">
                            <div class="icon-box p-3 rounded-3" style="background: rgba(255, 71, 61, 0.1); color: var(--primary);">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <div class="text-muted small font-weight-bold">Member Since</div>
                                <div class="font-weight-bold text-dark">{{ $user->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>

                        <div class="p-4 bg-white rounded-4 mb-3 shadow-sm d-flex align-items-center gap-3">
                            <div class="icon-box p-3 rounded-3" style="background: rgba(124, 90, 194, 0.1); color: var(--secondary);">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <div class="text-muted small font-weight-bold">Account Role</div>
                                <div class="font-weight-bold text-dark">{{ ucfirst($user->role ?? 'Admin') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 rounded-4 p-4" style="background: #eff6ff; color: #1e40af;">
                        <h5 class="font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Pro Tip</h5>
                        <p class="small mb-0">Complete your profile to build trust with your team and customers. A professional photo goes a long way!</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
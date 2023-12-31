<?php

namespace Masroore\SocialAuth\Support;

enum Feature: string
{
    case Registration = 'registration';
    case RefreshOauthTokens = 'refresh-oauth-tokens';
    case RememberSession = 'remember-session';
    case UpdateProfile = 'update-profile';
    case MarkEmailVerified = 'mark-email-verified';
    case ProfilePhoto = 'profile-photo';
    case ResizeProfilePhoto = 'resize-profile-photo';
    case CreateAccountOnFirstLogin = 'create-account-on-first-login';
    case LoginOnRegistration = 'login-on-registration';
    case GenerateMissingEmails = 'generate-missing-emails';
}

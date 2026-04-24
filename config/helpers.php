<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header("Location: $path");
    exit;
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function decode_cv($json) {
    $data = json_decode($json ?: '{}', true);
    return is_array($data) ? $data : [];
}

function app_translations(): array {
    return [
        'en' => [
            'lang_code' => 'en',
            'lang_name_en' => 'English',
            'lang_name_fr' => 'Français',
            'dashboard' => 'Dashboard',
            'your_cvs' => 'Your CVs',
            'new_cv' => '+ New CV',
            'hello' => 'Hello',
            'logout' => 'Logout',
            'design' => 'Design',
            'updated' => 'Updated',
            'edit' => 'Edit',
            'view_print' => 'View/Print',
            'delete' => 'Delete',
            'delete_confirm' => 'Delete this CV?',
            'click_to_edit' => 'Click to edit',
            'no_headline' => 'No headline yet',
            'login' => 'Log in',
            'email' => 'Email',
            'phone' => 'Phone',
            'password' => 'Password',
            'no_account' => 'No account?',
            'create_one' => 'Create one',
            'wrong_login' => 'Wrong email or password.',
            'register' => 'Register',
            'create_account' => 'Create account',
            'name' => 'Name',
            'already_registered' => 'Already registered?',
            'register_error' => 'Use a valid name, email, and password of at least 8 characters.',
            'email_exists' => 'That email is already registered.',
            'cv_not_found' => 'CV not found',
            'back' => 'Dashboard',
            'print_save_pdf' => 'Print / Save PDF',
            'edit_cv' => 'Edit CV',
            'new_cv_title' => 'New CV',
            'edit_your_cv' => 'Edit your CV',
            'create_new_cv' => 'Create a new CV',
            'live_cv_builder' => 'Live CV builder',
            'build_clean_cv' => 'Build a clean printable CV',
            'builder_description' => 'Choose a layout, fill your information, preview the result, then save or export as PDF.',
            'save_cv' => 'Print CV',
            'style' => 'Style',
            'cv_title' => 'CV title',
            'cv_title_placeholder' => 'My cybersecurity CV',
            'name_headline_position' => 'Name + headline position',
            'main_content' => 'Main content',
            'sidebar' => 'Sidebar',
            'photo' => 'Photo',
            'photo_help' => 'Use PNG, JPG or WEBP. The photo will be cropped automatically.',
            'identity' => 'Identity',
            'personal_details' => 'Personal details',
            'full_name' => 'Full name',
            'headline' => 'Headline',
            'headline_placeholder' => 'Alternance Cybersecurity Junior',
            'location' => 'Location',
            'website' => 'Website',
            'linkedin' => 'LinkedIn',
            'nationality' => 'Nationality',
            'internship_rhythm' => 'Internship rhythm',
            'rhythm_placeholder' => '1 week school / 2 weeks company',
            'driving_licence' => 'Driving licence',
            'driving_placeholder' => 'B, B1, B2...',
            'extra_personal_details' => 'Extra personal details',
            'extra_personal_placeholder' => "Portfolio: my portfolio\nAvailability: ASAP",
            'profile_summary' => 'Profile / Summary',
            'summary_placeholder' => 'Write a short professional profile...',
            'capabilities' => 'Capabilities',
            'skills_languages' => 'Skills & languages',
            'skills' => 'Skills',
            'skills_placeholder' => 'Security, Linux, PHP, MySQL',
            'languages' => 'Languages',
            'languages_placeholder' => "English - C1\nFrench - B2",
            'layout' => 'Layout',
            'placement_order' => 'Placement and order',
            'placement_help' => 'Use comma-separated keys. Sidebar designs can place sections inside the left sidebar.',
            'main_order' => 'Main order',
            'sidebar_order' => 'Sidebar order',
            'available_keys' => 'Available keys: details, summary, experience, education, projects, skills, languages, qualities, custom',
            'section' => 'Section',
            'add' => '+ Add',
            'remove' => 'Remove',
            'experience' => 'Experience',
            'education' => 'Education',
            'projects' => 'Projects',
            'qualities' => 'Qualities',
            'additional_sections' => 'Additional sections',
            'role' => 'Role',
            'company' => 'Company',
            'dates' => 'Dates',
            'degree' => 'Degree',
            'school' => 'School',
            'project_name' => 'Project name',
            'quality_title' => 'Quality title',
            'section_title' => 'Section title',
            'description' => 'Description',
            'bullets_description' => 'Bullets or description',
            'details' => 'Details',
            'content' => 'Content',
            'live_preview' => 'Live preview',
            'modern' => 'Modern',
            'unsaved_changes' => 'Unsaved changes',
            'save_before_leaving' => 'Save your CV before leaving?',
            'save' => 'Save',
            'leave_without_saving' => 'Leave without saving',
            'stay' => 'Stay',
            'profile' => 'Profile',
            'professional_experience' => 'Professional experience',
            'personal_information' => 'Personal information',
            'additional_information' => 'Additional information',
            'complete_role_company' => 'Complete Role or Company first.',
            'complete_degree_school' => 'Complete Degree or School first.',
            'complete_project_name' => 'Complete Project name first.',
            'complete_quality_title' => 'Complete Quality title first.',
            'complete_section_title' => 'Complete Section title first.',
            'your_name' => 'Your Name',
            'your_headline' => 'Your headline',
            'your_profile_summary' => 'Your profile summary will appear here.',
            'icon' => 'Icon',
            'text_label' => 'Text label',
            'nothing' => 'Nothing',
            'template_modern' => 'Modern compact one-page',
            'template_classic' => 'Compact sidebar - fresh',
            'template_classic_dark' => 'Compact sidebar - graphite',
            'template_classic_blue' => 'Compact sidebar - slate',
            'template_classic_orange' => 'Compact sidebar - sand',
            'template_classic_compact' => 'Compact sidebar - dense',
            'template_classic_clean' => 'Compact sidebar - clean',
            'template_classic_line' => 'Compact sidebar - line',
            'template_academic_sidebar' => 'Academic sidebar',
            'template_label_modern' => 'Modern compact',
            'template_label_classic' => 'Sidebar fresh',
            'template_label_classic_dark' => 'Sidebar graphite',
            'template_label_classic_blue' => 'Sidebar slate',
            'template_label_classic_orange' => 'Sidebar sand',
            'template_label_classic_compact' => 'Sidebar dense',
            'template_label_classic_clean' => 'Sidebar clean',
            'template_label_classic_line' => 'Sidebar line',
            'template_label_academic_sidebar' => 'Academic sidebar',
            'photo_upload_failed' => 'Photo upload failed.',
            'photo_too_large' => 'Photo must be under 2 MB.',
            'photo_bad_type' => 'Photo must be JPG, PNG, or WEBP.',
            'photo_save_failed' => 'Could not save photo.',
        ],
        'fr' => [
            'lang_code' => 'fr',
            'lang_name_en' => 'English',
            'lang_name_fr' => 'Français',
            'dashboard' => 'Tableau de bord',
            'your_cvs' => 'Vos CV',
            'new_cv' => '+ Nouveau CV',
            'hello' => 'Bonjour',
            'logout' => 'Déconnexion',
            'design' => 'Design',
            'updated' => 'Mis à jour',
            'edit' => 'Modifier',
            'view_print' => 'Voir/Imprimer',
            'delete' => 'Supprimer',
            'delete_confirm' => 'Supprimer ce CV ?',
            'click_to_edit' => 'Cliquer pour modifier',
            'no_headline' => 'Aucun titre pour le moment',
            'login' => 'Connexion',
            'email' => 'E-mail',
            'phone' => 'Téléphone',
            'password' => 'Mot de passe',
            'no_account' => 'Pas de compte ?',
            'create_one' => 'Créer un compte',
            'wrong_login' => 'E-mail ou mot de passe incorrect.',
            'register' => 'S’inscrire',
            'create_account' => 'Créer un compte',
            'name' => 'Nom',
            'already_registered' => 'Déjà inscrit ?',
            'register_error' => 'Utilisez un nom, un e-mail et un mot de passe valides d’au moins 8 caractères.',
            'email_exists' => 'Cet e-mail est déjà inscrit.',
            'cv_not_found' => 'CV introuvable',
            'back' => 'Tableau de bord',
            'print_save_pdf' => 'Imprimer / Enregistrer en PDF',
            'edit_cv' => 'Modifier le CV',
            'new_cv_title' => 'Nouveau CV',
            'edit_your_cv' => 'Modifier votre CV',
            'create_new_cv' => 'Créer un nouveau CV',
            'live_cv_builder' => 'Créateur de CV en direct',
            'build_clean_cv' => 'Créez un CV propre et imprimable',
            'builder_description' => 'Choisissez un modèle, remplissez vos informations, prévisualisez le résultat, puis enregistrez ou exportez en PDF.',
            'save_cv' => 'Imprimer le CV',
            'style' => 'Style',
            'cv_title' => 'Titre du CV',
            'cv_title_placeholder' => 'Mon CV cybersécurité',
            'name_headline_position' => 'Position du nom et du titre',
            'main_content' => 'Contenu principal',
            'sidebar' => 'Barre latérale',
            'photo' => 'Photo',
            'photo_help' => 'Utilisez PNG, JPG ou WEBP. La photo sera recadrée automatiquement.',
            'identity' => 'Identité',
            'personal_details' => 'Informations personnelles',
            'full_name' => 'Nom complet',
            'headline' => 'Titre',
            'headline_placeholder' => 'Alternance Cybersécurité Junior',
            'location' => 'Lieu',
            'website' => 'Site web',
            'linkedin' => 'LinkedIn',
            'nationality' => 'Nationalité',
            'internship_rhythm' => 'Rythme d’alternance',
            'rhythm_placeholder' => '1 semaine école / 2 semaines entreprise',
            'driving_licence' => 'Permis de conduire',
            'driving_placeholder' => 'B, B1, B2...',
            'extra_personal_details' => 'Informations personnelles supplémentaires',
            'extra_personal_placeholder' => "Portfolio : mon portfolio\nDisponibilité : ASAP",
            'profile_summary' => 'Profil / Résumé',
            'summary_placeholder' => 'Rédigez un court profil professionnel...',
            'capabilities' => 'Compétences',
            'skills_languages' => 'Compétences et langues',
            'skills' => 'Compétences',
            'skills_placeholder' => 'Sécurité, Linux, PHP, MySQL',
            'languages' => 'Langues',
            'languages_placeholder' => "Anglais - C1\nFrançais - B2",
            'layout' => 'Mise en page',
            'placement_order' => 'Placement et ordre',
            'placement_help' => 'Utilisez des clés séparées par des virgules. Les modèles avec barre latérale peuvent placer des sections dans la barre de gauche.',
            'main_order' => 'Ordre principal',
            'sidebar_order' => 'Ordre de la barre latérale',
            'available_keys' => 'Clés disponibles : details, summary, experience, education, projects, skills, languages, qualities, custom',
            'section' => 'Section',
            'add' => '+ Ajouter',
            'remove' => 'Supprimer',
            'experience' => 'Expérience',
            'education' => 'Formation',
            'projects' => 'Projets',
            'qualities' => 'Qualités',
            'additional_sections' => 'Sections supplémentaires',
            'role' => 'Poste',
            'company' => 'Entreprise',
            'dates' => 'Dates',
            'degree' => 'Diplôme',
            'school' => 'École',
            'project_name' => 'Nom du projet',
            'quality_title' => 'Nom de la qualité',
            'section_title' => 'Titre de la section',
            'description' => 'Description',
            'bullets_description' => 'Puces ou description',
            'details' => 'Détails',
            'content' => 'Contenu',
            'live_preview' => 'Aperçu en direct',
            'modern' => 'Moderne',
            'unsaved_changes' => 'Modifications non enregistrées',
            'save_before_leaving' => 'Enregistrer votre CV avant de quitter ?',
            'save' => 'Enregistrer',
            'leave_without_saving' => 'Quitter sans enregistrer',
            'stay' => 'Rester',
            'profile' => 'Profil',
            'professional_experience' => 'Expérience professionnelle',
            'personal_information' => 'Informations personnelles',
            'additional_information' => 'Informations supplémentaires',
            'complete_role_company' => 'Complétez le poste ou l’entreprise d’abord.',
            'complete_degree_school' => 'Complétez le diplôme ou l’école d’abord.',
            'complete_project_name' => 'Complétez le nom du projet d’abord.',
            'complete_quality_title' => 'Complétez le titre de la qualité d’abord.',
            'complete_section_title' => 'Complétez le titre de la section d’abord.',
            'your_name' => 'Votre nom',
            'your_headline' => 'Votre titre',
            'your_profile_summary' => 'Votre résumé professionnel apparaîtra ici.',
            'icon' => 'Icône',
            'text_label' => 'Libellé texte',
            'nothing' => 'Rien',
            'template_modern' => 'Moderne compact une page',
            'template_classic' => 'Barre latérale compacte - fraîche',
            'template_classic_dark' => 'Barre latérale compacte - graphite',
            'template_classic_blue' => 'Barre latérale compacte - ardoise',
            'template_classic_orange' => 'Barre latérale compacte - sable',
            'template_classic_compact' => 'Barre latérale compacte - dense',
            'template_classic_clean' => 'Barre latérale compacte - épurée',
            'template_classic_line' => 'Barre latérale compacte - ligne',
            'template_academic_sidebar' => 'Barre latérale académique',
            'template_label_modern' => 'Moderne compact',
            'template_label_classic' => 'Barre latérale fraîche',
            'template_label_classic_dark' => 'Barre latérale graphite',
            'template_label_classic_blue' => 'Barre latérale ardoise',
            'template_label_classic_orange' => 'Barre latérale sable',
            'template_label_classic_compact' => 'Barre latérale dense',
            'template_label_classic_clean' => 'Barre latérale épurée',
            'template_label_classic_line' => 'Barre latérale ligne',
            'template_label_academic_sidebar' => 'Barre latérale académique',
            'photo_upload_failed' => 'Échec du téléversement de la photo.',
            'photo_too_large' => 'La photo doit faire moins de 2 Mo.',
            'photo_bad_type' => 'La photo doit être au format JPG, PNG ou WEBP.',
            'photo_save_failed' => 'Impossible d’enregistrer la photo.',
        ],
    ];
}

function current_lang(): string {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'fr'], true)) {
        $_SESSION['lang'] = $_GET['lang'];
    }

    return $_SESSION['lang'] ?? 'en';
}

function t(string $key): string {
    $translations = app_translations();
    $lang = current_lang();

    return $translations[$lang][$key] ?? $translations['en'][$key] ?? $key;
}

function js_translations(): array {
    $keys = [
        'email','phone','location','website','linkedin','nationality','internship_rhythm','driving_licence',
        'template_label_modern','template_label_classic','template_label_classic_dark','template_label_classic_blue',
        'template_label_classic_orange','template_label_classic_compact','template_label_classic_clean','template_label_classic_line','template_label_academic_sidebar',
        'profile','professional_experience','education','projects','skills','languages','qualities','personal_information',
        'complete_role_company','complete_degree_school','complete_project_name','complete_quality_title','complete_section_title',
        'your_name','your_headline','your_profile_summary','remove','role','company','dates','bullets_description',
        'degree','school','details','project_name','description','quality_title','section_title','content'
    ];

    $out = [];
    foreach ($keys as $key) {
        $out[$key] = t($key);
    }
    return $out;
}

function lang_url(string $lang): string {
    $params = $_GET;
    $params['lang'] = $lang;
    $query = http_build_query($params);
    $script = basename($_SERVER['PHP_SELF']);
    return $script . ($query ? '?' . $query : '');
}

function language_switcher(): string {
    $current = current_lang();
    $enClass = $current === 'en' ? 'active' : '';
    $frClass = $current === 'fr' ? 'active' : '';

    return '<span class="language-switcher">'
        . '<a class="' . $enClass . '" href="' . e(lang_url('en')) . '">EN</a>'
        . '<span> | </span>'
        . '<a class="' . $frClass . '" href="' . e(lang_url('fr')) . '">FR</a>'
        . '</span>';
}

function template_label(string $template): string {
    $key = 'template_label_' . $template;
    return t($key) !== $key ? t($key) : ucfirst(str_replace('_', ' ', $template));
}

function upload_photo($file) {
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException(t('photo_upload_failed'));
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException(t('photo_too_large'));
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException(t('photo_bad_type'));
    }

    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $target = __DIR__ . '/../uploads/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException(t('photo_save_failed'));
    }
    return 'uploads/' . $name;
}

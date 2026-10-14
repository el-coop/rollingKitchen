<?php

/*
|--------------------------------------------------------------------------
| Settings defaults
|--------------------------------------------------------------------------
|
| Settings that are added to database/settings.json when they are missing,
| so new settings work on servers where the SettingsSeeder is not run.
| Existing values are never overwritten. Admins edit them at /admin/settings.
|
*/

return [
    'application_drinks_popup_nl' => 'Wil je naast je gerechten ook (non-alcoholische) dranken verkopen? Daarvoor betaal je een eenmalige toeslag, de SapSchenkpas (€ 1.000 ex BTW).',
    'application_drinks_popup_en' => 'Would you like to sell (non-alcoholic) drinks alongside your dishes? This comes with a one-off fee, the Juice Serving Pass (€ 1,000 ex VAT).',
    'application_drinks_fee' => 1000,

    'application_selected_subject_nl' => 'Je bent geselecteerd voor Rollende Keukens',
    'application_selected_subject_en' => 'You have been selected for Rollende Keukens',
    'application_selected_email_nl' => 'Je aanmelding is geselecteerd. Om je deelname definitief te maken hebben we nog wat extra informatie van je nodig. Log in en vul het formulier "Extra informatie" in.',
    'application_selected_email_en' => 'Your application has been selected. To finalise your participation we need some additional information from you. Please log in and fill in the "Additional information" form.',
    'application_extra_info_text_nl' => 'Je aanmelding is geselecteerd. Vul hieronder de extra informatie in en dien deze in.',
    'application_extra_info_text_en' => 'Your application has been selected. Please fill in and submit the additional information below.',
    'application_extra_info_menu_text_nl' => 'Alle keukens moeten beschikken over een allergenenlijst van hun menu. In de praktijk merken we echter dat er daarnaast steeds vaker vragen zijn over voeding die is bereid volgens bepaalde religieuze voorschriften en over veganistische voeding. Daarom stellen we hierover enkele aanvullende vragen, zodat we onze bezoekers hierover goed en duidelijk kunnen informeren.',
    'application_extra_info_menu_text_en' => 'All kitchens are required to have an allergen list for their menu. In practice, however, we have noticed that we are increasingly receiving questions about food prepared according to certain religious dietary requirements, as well as vegan food. That is why we are asking a few additional questions about these topics, so that we can provide our visitors with clear and accurate information.',
];

<?php

namespace App\Traits;

trait FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This trait basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo(){

        $data['depositVerify'] = [
            'path'      =>'assets/images/verify/deposit'
        ];
        $data['verify'] = [
            'path'      =>'assets/verify'
        ];
        $data['default'] = [
            'path'      => 'assets/images/general/default.png',
        ];

        $data['ticket'] = [
            'path'      => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path'      => 'assets/images/general',
        ];
        $data['favicon'] = [
            'size'      => '128x128',
        ];
        $data['extensions'] = [
            'path'      => 'assets/images/plugins',
            'size'      => '36x36',
        ];
        $data['seo'] = [
            'path'      => 'assets/images/seo',
            'size'      => '1180x600',
        ];
        $data['userProfile'] = [
            'path'      =>'assets/images/user/profile',
            'size'      =>'350x300',
        ];
        $data['adminProfile'] = [
            'path'      =>'assets/admin/images/profile',
            'size'      =>'400x400',
        ];

        $data['banner'] = [
            'path'      =>'assets/images/frontend/banner',
        ];
        $data['ThemeThreeBanner'] = [
            'path'      =>'assets/images/frontend/theme_three_banner',
        ];
        $data['about'] = [
            'path'      =>'assets/images/frontend/about',
        ];
        $data['ThemeTwoAbout'] = [
            'path'      =>'assets/images/frontend/theme_two_about',
        ];
        $data['ThemeThreeAbout'] = [
            'path'      =>'assets/images/frontend/theme_three_about',
        ];
        $data['chooseus'] = [
            'path'      =>'assets/images/frontend/choose_us',
        ];
        $data['ThemeTwoChooseus'] = [
            'path'      =>'assets/images/frontend/theme_two_choose_us',
        ];
        $data['ThemeThreeChooseus'] = [
            'path'      =>'assets/images/frontend/theme_three_choose_us',
        ];
        $data['portfolio'] = [
            'path'      =>'assets/images/frontend/portfolio',
        ];

        $data['ThemeTwoPortfolio'] = [
            'path'      =>'assets/images/frontend/theme_two_portfolio',
        ];
        $data['ThemeThreePortfolio'] = [
            'path'      =>'assets/images/frontend/theme_three_portfolio',
        ];
        $data['teamMember'] = [
            'path'      =>'assets/images/frontend/team_member',
        ];
        $data['testimonial'] = [
            'path'      =>'assets/images/frontend/testimonial',
        ];
        $data['subscribe'] = [
            'path'      =>'assets/images/frontend/subscribe',
        ];
        $data['faq'] = [
            'path'      =>'assets/images/frontend/faq',
        ];
        $data['blog'] = [
            'path'      =>'assets/images/frontend/blog',
        ];
        $data['brand'] = [
            'path'      =>'assets/images/frontend/brand',
        ];
        $data['contact_us'] = [
            'path'      =>'assets/images/frontend/contact_us',
        ];

        $data['portfolioImage'] = [
            'path'      =>'assets/images/frontend/portfolioImage',
            'size'     =>'416x417',
        ];
        $data['serviceFile'] = [
            'path'      =>'assets/images/frontend/serviceFile',
        ];

        $data['frontend'] = [
            'path'      =>'assets/images/frontend',
        ];
        $data['MpProfile'] = [
            'path'      =>'assets/admin/images/mp/profile',
            'size'      =>'400x400',
        ];
        $data['founderProfile'] = [
            'path'      =>'assets/admin/images/founder/profile',
            'size'      =>'400x400',
        ];
        $data['MlaProfile'] = [
            'path'      =>'assets/admin/images/mla/profile',
            'size'      =>'400x400',
        ];

        $data['VidhanSabhaAdminProfile'] = [
            'path'      =>'assets/admin/images/vidhansabha-admin/profile',
            'size'      =>'400x400',
        ];

        $data['QuestionsImage'] = [
            'path'      =>'assets/admin/images/question',
        ];

        $data['BlogImage'] = [
            'path'      =>'assets/admin/images/blogs',
        ];
        $data['SpottersImage'] = [
            'path'      =>'assets/admin/images/spotters',
        ];
        $data['OsceImage'] = [
            'path'      =>'assets/admin/images/osce',
        ];

        $data['NewSpottersImage'] = [
            'path'      =>'assets/admin/images/new_spotters',
        ];

        $data['NewSpottersPDF'] = [
            'path'      =>'assets/admin/images/new_spotters_pdf',
        ];

        $data['NewOsceImage'] = [
            'path'      =>'assets/admin/images/new_osce',
        ];

        $data['NewOscePDF'] = [
            'path'      =>'assets/admin/images/new_osce_pdf',
        ];

        $data['PracticalEssentialsImage'] = [
            'path'      =>'assets/admin/images/practical_essentials',
        ];

        $data['PracticalEssentialsPDF'] = [
            'path'      =>'assets/admin/images/practical_essentials_pdf',
        ];

        $data['AiRadsImage'] = [
            'path'      =>'assets/admin/images/ai_rads',
        ];

        $data['AiRadsPDF'] = [
            'path'      =>'assets/admin/images/ai_rads_pdf',
        ];

        $data['NewExamCasesImage'] = [
            'path'      =>'assets/new_exam_cases_pdf',
        ];

        $data['NewExamCasesPDF'] = [
            'path'      =>'assets/new_exam_cases_pdf',
        ];

        $data['NewTableVivaImage'] = [
            'path'      =>'assets/new_table_viva_pdf',
        ];

        $data['NewTableVivaPDF'] = [
            'path'      =>'assets/new_table_viva_pdf',
        ];

        $data['TheoryNotesImage'] = [
            'path'      =>'assets/theory_notes_pdf',
        ];

        $data['TheoryNotesPDF'] = [
            'path'      =>'assets/theory_notes_pdf',
        ];

        $data['CKImage'] = [
            'path'      =>'assets/admin/images/editor',
        ];



        $data['BannerImage'] = [
            'path'      =>'assets/admin/images/banners',
            'size'      =>'400x400',
        ];

        $data['DonationImage'] = [
            'path'      =>'assets/admin/images/donation',
            'size'      =>'400x400',
        ];

        $data['PaymentQR'] = [
            'path'      =>'assets/admin/images/PaymentQR',
            'size'      =>'400x400',
        ];

        return $data;
	}

}

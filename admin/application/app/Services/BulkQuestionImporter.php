<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BulkQuestionImporter
{
    private $errors = [];
    private $warnings = [];
    private $uploadDir;

    public function __construct()
    {
        $this->uploadDir = getFilePath('QuestionsImage') . '/';
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Generate Excel template for bulk upload
     */
    public function generateTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Quiz Questions');

        // Set column headers
        $headers = [
            'A' => 'ID',
            'B' => 'Question_Text',
            'C' => 'Question_Image',
            'D' => 'Marks',
            'E' => 'Option_A',
            'F' => 'Option_B',
            'G' => 'Option_C',
            'H' => 'Option_D',
            'I' => 'Option_E',
            'J' => 'Option_F',
            'K' => 'Correct_Answer',
            'L' => 'Explanation',
            'M' => 'Sort_Order'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0071C5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(8);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(50);
        $sheet->getColumnDimension('M')->setWidth(12);

        // Add sample questions
        $samples = [
            ['Q001', 'What is the normal heart rate in adults?', '', '1', '60-100 bpm', '100-120 bpm', '40-60 bpm', '120-140 bpm', '', '', 'A', 'Normal adult resting heart rate is 60-100 beats per minute', '1'],
            ['Q002', 'The liver is located in which quadrant?', '', '1', 'Right upper', 'Left upper', 'Right lower', 'Left lower', '', '', 'A', 'The liver is primarily located in the right upper quadrant of the abdomen', '2'],
            ['Q003', 'Which of the following are symptoms of diabetes? (Multiple answers)', '', '2', 'Increased thirst', 'Increased urination', 'Weight loss', 'Headache', '', '', 'A,B,C', 'Classic symptoms of diabetes include polydipsia, polyuria, and unexplained weight loss', '3'],
        ];

        $row = 2;
        foreach ($samples as $sample) {
            $col = 'A';
            foreach ($sample as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Create Instructions sheet
        $instructionsSheet = $spreadsheet->createSheet();
        $instructionsSheet->setTitle('Instructions');
        
        $instructions = [
            ['Bulk Quiz Upload - Instructions'],
            [''],
            ['COLUMN DESCRIPTIONS:'],
            ['ID', 'Unique identifier for the question (e.g., Q001, Q002)'],
            ['Question_Text', 'The question text (required)'],
            ['Question_Image', 'Leave empty or paste image directly into cell'],
            ['Marks', 'Points for the question (1-10)'],
            ['Option_A to Option_F', 'Answer choices (text or images). At least 2 options required'],
            ['Correct_Answer', 'Letter(s) of correct answer(s). Examples: A  or  A,B,C  or  1,2  (comma-separated for multiple correct)'],
            ['Explanation', 'Explanation shown after answering'],
            ['Sort_Order', 'Display order (optional, will auto-increment if empty)'],
            [''],
            ['HOW TO USE:'],
            ['1. Fill in your questions starting from row 2'],
            ['2. For images: Right-click cell → Insert → Pictures → From File'],
            ['3. For multiple correct answers: Use comma-separated format like "A,B,C" or "1,2,3"'],
            ['4. Delete the sample rows before uploading'],
            ['5. Save file and upload through admin panel'],
            [''],
            ['EXAMPLES:'],
            ['Single answer: Correct_Answer = "A"'],
            ['Multiple answers: Correct_Answer = "A,C,E"'],
            ['Numeric format: Correct_Answer = "1,2" (converts to A,B)'],
        ];

        $row = 1;
        foreach ($instructions as $instruction) {
            $instructionsSheet->fromArray($instruction, null, 'A' . $row);
            $row++;
        }

        $instructionsSheet->getColumnDimension('A')->setWidth(25);
        $instructionsSheet->getColumnDimension('B')->setWidth(80);
        $instructionsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionsSheet->getStyle('A3')->getFont()->setBold(true);
        $instructionsSheet->getStyle('A13')->getFont()->setBold(true);
        $instructionsSheet->getStyle('A19')->getFont()->setBold(true);

        return $spreadsheet;
    }

    /**
     * Parse uploaded Excel file
     */
    public function parseExcelFile($filePath)
    {
        $this->errors = [];
        $this->warnings = [];
        $questions = [];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $questionData = $this->parseRow($sheet, $row);
                if ($questionData) {
                    $questions[] = $questionData;
                }
            }

            return [
                'success' => true,
                'questions' => $questions,
                'errors' => $this->errors,
                'warnings' => $this->warnings
            ];
        } catch (\Exception $e) {
            $this->errors[] = ['message' => 'Failed to parse Excel file: ' . $e->getMessage()];
            return [
                'success' => false,
                'questions' => [],
                'errors' => $this->errors,
                'warnings' => $this->warnings
            ];
        }
    }

    /**
     * Parse individual row
     */
    private function parseRow($sheet, $rowNum)
    {
        $id = trim($sheet->getCell('A' . $rowNum)->getValue());
        
        // Skip empty rows
        if (empty($id)) {
            return null;
        }

        $questionText = trim($sheet->getCell('B' . $rowNum)->getValue());
        $marks = trim($sheet->getCell('D' . $rowNum)->getValue());
        $correctAnswer = trim($sheet->getCell('K' . $rowNum)->getValue());
        $explanation = trim($sheet->getCell('L' . $rowNum)->getValue());
        $sortOrder = trim($sheet->getCell('M' . $rowNum)->getValue());

        $questionData = [
            'row' => $rowNum,
            'id' => $id,
            'question_text' => $questionText,
            'marks' => !empty($marks) ? intval($marks) : 1,
            'explanation' => $explanation,
            'sort_order' => !empty($sortOrder) ? intval($sortOrder) : 0,
            'question_image' => null,
            'options' => [],
            'errors' => [],
            'warnings' => []
        ];

        // Validate required fields
        if (empty($questionText)) {
            $questionData['errors'][] = 'Question text is required';
            $this->errors[] = ['row' => $rowNum, 'id' => $id, 'message' => 'Question text is required'];
        }

        if (empty($correctAnswer)) {
            $questionData['errors'][] = 'Correct answer is required';
            $this->errors[] = ['row' => $rowNum, 'id' => $id, 'message' => 'Correct answer is required'];
        }

        // Extract question image
        $questionImage = $this->extractImageFromCell($sheet, 'C' . $rowNum);
        if ($questionImage) {
            $questionData['question_image'] = $questionImage;
        }

        // Parse options (E to J = A to F)
        $optionColumns = ['E' => 'A', 'F' => 'B', 'G' => 'C', 'H' => 'D', 'I' => 'E', 'J' => 'F'];
        $optionOrder = 1;
        
        foreach ($optionColumns as $col => $label) {
            $optionValue = trim($sheet->getCell($col . $rowNum)->getValue());
            if (!empty($optionValue)) {
                // Check if it's an image or text
                $optionImage = $this->extractImageFromCell($sheet, $col . $rowNum);
                
                $questionData['options'][] = [
                    'label' => $label,
                    'option_order' => $optionOrder,
                    'option_text' => empty($optionImage) ? $optionValue : '',
                    'option_image' => $optionImage,
                    'is_correct' => 0  // Will be set based on correct answer
                ];
                $optionOrder++;
            }
        }

        // Validate at least 2 options
        if (count($questionData['options']) < 2) {
            $questionData['errors'][] = 'At least 2 options are required';
            $this->errors[] = ['row' => $rowNum, 'id' => $id, 'message' => 'At least 2 options are required'];
        }

        // Parse correct answers (comma-separated)
        if (!empty($correctAnswer)) {
            $correctAnswers = array_map('trim', explode(',', strtoupper($correctAnswer)));
            
            foreach ($correctAnswers as $ans) {
                // Convert numbers to letters (1=>A, 2=>B, etc.)
                if (is_numeric($ans)) {
                    $ansNum = intval($ans);
                    $ans = chr(64 + $ansNum);  // 65 is 'A' in ASCII
                }
                
                // Find matching option and mark as correct
                $found = false;
                foreach ($questionData['options'] as &$option) {
                    if ($option['label'] === $ans) {
                        $option['is_correct'] = 1;
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $questionData['errors'][] = "Correct answer '$ans' does not match any option";
                    $this->errors[] = ['row' => $rowNum, 'id' => $id, 'message' => "Correct answer '$ans' does not match any option"];
                }
            }
        }

        return $questionData;
    }

    /**
     * Extract image from Excel cell
     */
    private function extractImageFromCell($sheet, $cellCoordinate)
    {
        try {
            $drawings = $sheet->getDrawingCollection();
            
            foreach ($drawings as $drawing) {
                if ($drawing->getCoordinates() === $cellCoordinate) {
                    if (method_exists($drawing, 'getPath')) {
                        $imagePath = $drawing->getPath();
                        
                        // Excel images are stored in ZIP archive
                        $imageContents = @file_get_contents($imagePath);
                        
                        if ($imageContents !== false && !empty($imageContents)) {
                            // Determine extension
                            $extension = $drawing->getExtension();
                            if (empty($extension)) {
                                $extension = 'png';
                            }
                            
                            // Generate unique filename
                            $filename = uniqid('img_') . '.' . $extension;
                            $filepath = $this->uploadDir . $filename;
                            
                            // Save image
                            file_put_contents($filepath, $imageContents);
                            
                            return $filename;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("Image extraction error: " . $e->getMessage());
        }
        
        return null;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }
}

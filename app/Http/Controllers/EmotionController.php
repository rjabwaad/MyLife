<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmotionController extends Controller
{
    public function analyze(Request $request)
    {
        $text = strtolower($request->input('text'));

        // Analyseur de sentiments local basé sur des mots-clés
        $emotionKeywords = [
            'joy' => [
                'keywords' => ['happy', 'joy', 'joyful', 'excited', 'wonderful', 'amazing', 'great', 'fantastic', 'excellent', 'love', 'loving', 'loved', 'delighted', 'cheerful', 'pleased', 'glad', 'thrilled', 'ecstatic', 'heureux', 'joie', 'content', 'ravi'],
                'weight' => 2
            ],
            'sadness' => [
                'keywords' => ['sad', 'sadness', 'unhappy', 'depressed', 'lonely', 'alone', 'miserable', 'sorrow', 'grief', 'heartbroken', 'disappointed', 'down', 'blue', 'gloomy', 'triste', 'tristesse', 'malheureux', 'seul'],
                'weight' => 2
            ],
            'anger' => [
                'keywords' => ['angry', 'anger', 'mad', 'furious', 'rage', 'annoyed', 'irritated', 'frustrated', 'hate', 'hatred', 'outraged', 'enraged', 'livid', 'colère', 'fâché', 'énervé'],
                'weight' => 2
            ],
            'fear' => [
                'keywords' => ['scared', 'fear', 'afraid', 'frightened', 'terrified', 'worried', 'anxious', 'nervous', 'panic', 'horror', 'dread', 'alarmed', 'peur', 'effrayé', 'inquiet', 'anxieux'],
                'weight' => 2
            ],
            'surprise' => [
                'keywords' => ['surprised', 'surprise', 'shocked', 'amazed', 'astonished', 'stunned', 'wow', 'omg', 'unbelievable', 'incredible', 'unexpected', 'surpris', 'étonné', 'choqué'],
                'weight' => 2
            ],
            'disgust' => [
                'keywords' => ['disgusting', 'disgust', 'gross', 'nasty', 'revolting', 'repulsive', 'awful', 'terrible', 'horrible', 'yuck', 'ew', 'dégoûtant', 'dégoût', 'horrible'],
                'weight' => 2
            ],
            'neutral' => [
                'keywords' => ['okay', 'ok', 'fine', 'normal', 'regular', 'average', 'went', 'did', 'was', 'is', 'are', 'the', 'a', 'an'],
                'weight' => 1
            ]
        ];

        // Calculer les scores pour chaque émotion
        $scores = [];
        foreach ($emotionKeywords as $emotion => $data) {
            $score = 0;
            foreach ($data['keywords'] as $keyword) {
                // Compter le nombre d'occurrences du mot-clé
                $count = substr_count($text, $keyword);
                $score += $count * $data['weight'];
            }
            $scores[$emotion] = $score;
        }

        // Trouver l'émotion dominante
        $maxScore = max($scores);

        // Si aucun mot-clé trouvé, retourner neutral
        if ($maxScore == 0) {
            return response()->json([
                'emotion' => 'neutral',
                'confidence' => 0.50
            ]);
        }

        // Trouver l'émotion avec le score le plus élevé
        $dominantEmotion = array_search($maxScore, $scores);

        // Calculer la confiance (score / total des scores)
        $totalScore = array_sum($scores);
        $confidence = $totalScore > 0 ? ($maxScore / $totalScore) : 0.50;

        // Ajuster la confiance pour qu'elle soit entre 0.60 et 0.95
        $confidence = max(0.60, min(0.95, $confidence));

        return response()->json([
            'emotion' => $dominantEmotion,
            'confidence' => round($confidence, 2)
        ]);
    }
}

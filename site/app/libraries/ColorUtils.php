<?php

namespace app\libraries;

class ColorUtils {
    // TODO: give colors names :)
    // TODO: do we really want 20 colors, or narrow down to 7 distinct ones?
    const COLORS = [
        "category-color-1" => "var(--category-color-1)",
        "category-color-2" => "var(--category-color-2)",
        "category-color-3" => "var(--category-color-3)",
        "category-color-4" => "var(--category-color-4)",
        "category-color-5" => "var(--category-color-5)",
        "category-color-6" => "var(--category-color-6)",
        "category-color-7" => "var(--category-color-7)",
        "category-color-8" => "var(--category-color-8)",
        "category-color-9" => "var(--category-color-9)",
        "category-color-10" => "var(--category-color-10)",
        "category-color-11" => "var(--category-color-11)",
        "category-color-12" => "var(--category-color-12)",
        "category-color-13" => "var(--category-color-13)",
        "category-color-14" => "var(--category-color-14)",
        "category-color-15" => "var(--category-color-15)",
        "category-color-16" => "var(--category-color-16)",
        "category-color-17" => "var(--category-color-17)",
        "category-color-18" => "var(--category-color-18)",
        "category-color-19" => "var(--category-color-19)",
        "category-color-20" => "var(--category-color-20)"
    ];

    public static function getColorNames() {
        return array_keys(self::COLORS);
    }

    public static function getColorValues() {
        return array_values(self::COLORS);
    }
}

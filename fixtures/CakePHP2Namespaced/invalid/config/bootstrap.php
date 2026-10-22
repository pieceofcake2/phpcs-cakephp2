<?php
if (!extension_loaded('mbstring')) {
    throw new \RuntimeException('The mbstring extension is required');
}

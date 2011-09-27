#!/usr/bin/php
<?php

$expansion = <<<EOF
#define DECLARE(var) v##var: .word 0
#define VAR(var, reg) CONST($29, v##var)&-lw reg, 0($29)
#define STORE(var, reg) CONST($29, v##var)&-sw reg, 0($29)
#define CONST(reg, val) lis reg&-.word val
#define RETURN(reg, num, args ...) STORE(return, reg)&-POP(num, args)&-jr $31

#define PUSH(num, arg, args ...) EXPAND##num(sw, arg, args)&-\
    lis arg&-\
    .word -DEPTH##num&-\
    add $30, $30, arg
#define POP(num, arg, args ...) lis arg&-\
    .word DEPTH##num&-\
    add $30, $30, arg&-\
    EXPAND##num(lw, arg, args)
#define CALL(func) PUSH(1, $31)&-\
    lis $31&-\
    .word func&-\
    jalr $31&-\
    POP(1, $31)

#define DEPTH1 4
#define DEPTH2 8
#define DEPTH3 12
#define DEPTH4 16
#define DEPTH5 20
#define DEPTH6 24
#define DEPTH7 28
#define DEPTH8 32
#define DEPTH9 36
#define DEPTH10 40
#define DEPTH11 44
#define DEPTH12 48
#define DEPTH13 52
#define DEPTH14 56
#define DEPTH15 60
#define DEPTH16 64

#define EXPAND1(op,  arg, args ...) op arg, -4($30)
#define EXPAND2(op,  arg, args ...) op arg,  -8($30)&-EXPAND1(op, args)
#define EXPAND3(op,  arg, args ...) op arg, -12($30)&-EXPAND2(op, args)
#define EXPAND4(op,  arg, args ...) op arg, -16($30)&-EXPAND3(op, args)
#define EXPAND5(op,  arg, args ...) op arg, -20($30)&-EXPAND4(op, args)
#define EXPAND6(op,  arg, args ...) op arg, -24($30)&-EXPAND5(op, args)
#define EXPAND7(op,  arg, args ...) op arg, -28($30)&-EXPAND6(op, args)
#define EXPAND8(op,  arg, args ...) op arg, -32($30)&-EXPAND7(op, args)
#define EXPAND9(op,  arg, args ...) op arg, -36($30)&-EXPAND8(op, args)
#define EXPAND10(op, arg, args ...) op arg, -40($30)&-EXPAND9(op, args)
#define EXPAND11(op, arg, args ...) op arg, -44($30)&-EXPAND10(op, args)
#define EXPAND12(op, arg, args ...) op arg, -48($30)&-EXPAND11(op, args)
#define EXPAND13(op, arg, args ...) op arg, -52($30)&-EXPAND12(op, args)
#define EXPAND14(op, arg, args ...) op arg, -56($30)&-EXPAND13(op, args)
#define EXPAND15(op, arg, args ...) op arg, -60($30)&-EXPAND14(op, args)
#define EXPAND16(op, arg, args ...) op arg, -64($30)&-EXPAND15(op, args)

EOF;

$expansion = $expansion . file_get_contents($argv[1]) . "\nDECLARE(return)";
file_put_contents(".expander.c", $expansion);

$file = explode("\n", str_replace("&-", "\n", `gcc -E .expander.c`));
unlink(".expander.c");

foreach ($file as $line) {
    $line = trim($line);
    if (@$line[0] != '#')
        echo $line . "\n";
}

?>
#!/bin/sh

filename=$1

gcc -Wall -Werror $filename -I/Users/chiara/Projects/satc/github/usr/include -L/Users/chiara/Projects/satc/github/usr/lib -lbgpstream -o ./$(basename "$filename" .c)

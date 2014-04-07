VocabExtract
============

This program is a format specified text extraction program for the usage of LDA.

The link of LDA we will use is (http://www.cs.princeton.edu/~blei/lda-c/index.html).

The texts is recognised from a data structure called **JSON**

This program is divided into two parts:

  1 Vocaburary Extraction

  2 Count Vocaburary From Existing Documents

Format in **JSON**
------------------

For the vocab extraction, we will use a JSON File with the following attribute.

```
..., "categories": [(Vocabs)], ...

```



For the counting, we will use a JSON file with the following attribute.

```
..., "text": "(A BUNCH OF TEXT)", ...

```

#include "Str_cell.h"
#include "string.h"
#include <string>

Str_cell::Str_cell() {
  _str = NULL;
  _next = NULL;
  count = NULL;
}

Str_cell::Str_cell(char* str) {
  _str = new char[strlen(str) + 1];
  strcpy(_str, str);
  _next = NULL;
  count = NULL;
}

char* Str_cell::str() {
  return _str;
}

Str_cell* Str_cell::next() {
  return _next;
}

int Str_cell::getCount() {
  return *count;
}

void Str_cell::setNext(Str_cell* nextCell) {
  _next = nextCell;
}

void Str_cell::linkToCount(int* count_block) {
  count = count_block;
}

void Str_cell::addCount() {
  (*count)++;
}

void Str_cell::setZero() {
  *count = 0;
}

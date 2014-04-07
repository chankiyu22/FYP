#ifndef strcell_h
#define strcell_h
#include <cstddef>
#include "string.h"

/**
 * Class Str_cell
 * This class manage a linked list of string corresponding
 * to the character sum.
 *
 * Implementation:
 * A new Str_cell will be created by a string if that string
 * is new to that character sum. Then, it will be inserted
 * into the tail of the Str_cell list.
 *
 * No order is defined.
 */
class Str_cell {
public:
  Str_cell();
  Str_cell(char* );

  char* str();
  Str_cell* next();
  int getCount();

  void setNext(Str_cell* nextCell);
  void linkToCount(int* count_block);
  void addCount();
  void setZero();
private:
  char* _str;
  Str_cell* _next;
  int* count;
};

#endif

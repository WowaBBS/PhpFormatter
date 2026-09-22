<?
namespace Reformat\Linked;

Trait TWeak
{
  // Try Weak field and ToWeak to compare
  Function ToWeak() { Return \WeakReference::Create($this); }
}
import { ChildProcessWithoutNullStreams } from "child_process";

export interface ProcessResult {
  process: ChildProcessWithoutNullStreams;
  port: number;
}
